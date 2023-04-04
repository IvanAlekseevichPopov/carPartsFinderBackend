<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\CarModel;
use App\Entity\Part;
use App\Entity\PartName;
use App\Exception\TimeoutException;
use App\Repository\BrandRepository;
use App\Repository\CarModelRepository;
use App\Repository\PartNameRepository;
use App\Repository\PartRepository;
use App\Service\Locks;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(
    name: 'app:parts:find',
    description: 'Command to search parts and their images in the internet',
)]
class PartsNumbersSearchCommand extends Command
{
    use TimerTrait;

    private EntityManagerInterface $entityManager;
    private BrandRepository $brandRepository;
    private CarModelRepository $carModelRepository;
    private PartNameRepository $partNameRepository;
    private PartRepository $partRepository;
    private ClientInterface $client;
    private CacheItemPoolInterface $cache;
    private LockFactory $lockFactory;

    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $brandRepository,
        CarModelRepository $carModelRepository,
        PartNameRepository $partNameRepository,
        PartRepository $partRepository,
        ClientInterface $parserClient,
        CacheItemPoolInterface $dbCache,
        LockFactory $lockFactory,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
        $this->carModelRepository = $carModelRepository;
        $this->partNameRepository = $partNameRepository;
        $this->partRepository = $partRepository;
        $this->client = $parserClient;
        $this->cache = $dbCache;
        $this->lockFactory = $lockFactory;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('brand', InputArgument::OPTIONAL, 'Which brand to parse. Use brand name')
            ->addOption('ttl', 't', InputOption::VALUE_REQUIRED, 'Max lifetime of command in seconds', 600);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTimer($input->getOption('ttl'));

        $brand = $this->getBrand($input);
        try {
            if ($brand) {
                $this->logger->notice("Searching parts for single brand {$brand->getName()}");
                $models = $this->carModelRepository->findAllToParseByBrand($brand);

                $this->processModels($models);
            } else {
                $brands = $this->brandRepository->findAllToParseParts();
                $this->logger->notice('Searching parts for all brands: '.count($brands));

                foreach ($brands as $brand) {
                    $this->logger->notice("Searching parts for brand {$brand->getName()}");

                    $models = $this->carModelRepository->findAllToParseByBrand($brand);

                    $this->processModels($models);

                    $this->logger->notice("Done searching parts for brand {$brand->getName()}. Clearing entity manager");
                    $this->entityManager->clear();
                    gc_collect_cycles();
                }
            }
        } catch (TimeoutException) {
            $this->logger->notice('Time is out. Closing command..');
        }

        return Command::SUCCESS;
    }

    protected function processModels(array $models)
    {
        $this->logger->notice('Start parsing car models');
        foreach ($models as $model) {
            $lock = $this->lockFactory->createLock(Locks::PARSING_PARTS_MODEL.$model->getId());
            if (false === $lock->acquire()) {
                $this->logger->notice("Lock is already acquired. Skipping model {$model->getName()}");
                continue;
            }
            try {
                $this->processOneModel($model);
            } catch (\Throwable $e) {
                $lock->release();
                throw $e;
            }
        }
    }

    private function processOneModel(CarModel $model)
    {
        $this->logger->notice("Start parsing car model {$model->getName()}");
        $brand = $model->getBrand();
        $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications");
        $modificationsResponse = json_decode($res->getBody()->getContents(), true);

        foreach ($modificationsResponse['modifications'] as $rawModificationData) {
            if ('Мотоцикл' === $rawModificationData['constructionType']) {
                continue;
            }
            $this->logger->notice("Start parsing modification {$model->getName()} {$rawModificationData['name']} {$rawModificationData['constructionType']}");
            $alreadyProcessed = $this->cache->getItem("modification_{$rawModificationData['id']}");
            if ($alreadyProcessed->isHit() && true === $alreadyProcessed->get()) {
                $this->logger->notice("Modification {$rawModificationData['id']} already processed");
                continue;
            }
            usleep(random_int(10000, 100000));
            $nodesRes = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications/{$rawModificationData['id']}/nodes");

            $nodesResponse = json_decode($nodesRes->getBody()->getContents(), true);
            foreach ($nodesResponse['nodes'] as $rawNodeData) {
                $this->processNode($rawNodeData, $rawModificationData['id'], $model);
                if ($this->isTimeOut()) {
                    throw new TimeoutException();
                }
            }
            $alreadyProcessed->set(true);
            $this->cache->save($alreadyProcessed);
            $this->logger->notice('Finished modification. Clearing');
            $this->entityManager->flush();
            $this->entityManager->clear();
            gc_collect_cycles();
        }
        $model->setModifications($modificationsResponse['modifications']);
        $model->setChildrenPartsParsed(true);
        $this->entityManager->flush();

        $this->logger->notice("Start parsing car model {$model->getName()}");
    }

    private function processNode(array $rawNodeData, int $modificationId, CarModel $model)
    {
        $lock = $this->lockFactory->createLock(Locks::PARSING_PARTS_NODE.$rawNodeData['id'].'_'.$modificationId);
        if (false === $lock->acquire()) {
            $this->logger->notice("Lock is already acquired. Skipping node {$rawNodeData['id']}");

            return;
        }
        foreach ($rawNodeData['children'] as $rawNodeData) {
            $this->processNode($rawNodeData, $modificationId, $model);
        }

        $id = $rawNodeData['id'];
        $alreadyProcessed = $this->cache->getItem("node_{$modificationId}_{$id}");
        if ($alreadyProcessed->isHit()) {
            $this->logger->notice("Node {$modificationId}_{$id} already processed");

            return;
        }
        $brand = $model->getBrand();
        try {
            $sparePartsRes = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications/{$modificationId}/nodes/{$id}/spareparts");

            $sparePartsResponse = json_decode($sparePartsRes->getBody()->getContents(), true);

            foreach ($sparePartsResponse['spareParts'] as $rawSparePartData) {
                $brand = $this->findOrCreateBrand($rawSparePartData['brandName'], $rawSparePartData['autodocBrandId']);
                $partName = $this->findOrCreatePartName($rawSparePartData['name']);

                $this->findOrCreatePart($brand, $partName, $model, $rawSparePartData['partNumber'], $rawSparePartData['imageUrls'] ?? null);
            }
        } catch (ServerException|ConnectException $e) {
            $this->logger->warning('Guzzle error: '.$e->getMessage());

            $item = $this->cache->getItem('failed_nodes_'.$brand->getName());
            $value = $item->get() ?? [];
            $value[] = "/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications/{$modificationId}/nodes/{$id}/spareparts";
            $item->set($value);
        }

        $alreadyProcessed->set(true);
        $this->cache->save($alreadyProcessed);
        $lock->release();
    }

    private function findOrCreateBrand(string $brandName, int $autodocBrandId): Brand
    {
        $brand = $this->brandRepository->findOneBy(['externalId' => $autodocBrandId]);
        if (!$brand) {
            $brand = new Brand($brandName, $autodocBrandId);
            $this->entityManager->persist($brand);
        }

        return $brand;
    }

    private function findOrCreatePartName(string $name): PartName
    {
        $partName = $this->partNameRepository->findOneBy(['name' => $name]);
        if (!$partName) {
            $partName = new PartName($name);
            $this->entityManager->persist($partName);
        }

        return $partName;
    }

    private function findOrCreatePart(Brand $brand, PartName $partName, CarModel $model, string $partNumber, ?array $images = null): Part
    {
        $lock = $this->lockFactory->createLock(Locks::PARSING_PARTS_MODEL.$model->getId(), 10);
        $lock->acquire(true);

        $part = $this->partRepository->findOneBy(['partNumber' => $partNumber]);
        if (!$part) {
            $part = new Part($partNumber, $partName, $brand);
            $this->logger->notice("New part {$part->getPartNumber()}");
            if (!empty($images)) {
                $part->setImagesToParse($images);
            }
            $this->entityManager->persist($part);
        }
        // TODO optimistic lock here
        $part->addSuitableModel($model);

        try {
            $this->entityManager->flush();
        } catch (OptimisticLockException $e) {
            $this->logger->critical("Optimistic lock exception: {$e->getMessage()}");
        } finally {
            $lock->release();
        }

        return $part;
    }

    protected function getBrand(InputInterface $input): ?Brand
    {
        $brandName = $input->getArgument('brand');
        $brand = null;
        if ($brandName) {
            $brand = $this->brandRepository->findWithSimilarName($brandName);
        }

        return $brand;
    }
}
