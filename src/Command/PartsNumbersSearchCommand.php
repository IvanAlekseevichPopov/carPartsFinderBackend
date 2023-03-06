<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\CarModel;
use App\Entity\Part;
use App\Entity\PartName;
use App\Repository\BrandRepository;
use App\Repository\CarModelRepository;
use App\Repository\PartNameRepository;
use App\Repository\PartRepository;
use App\Service\Locks;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(
    name: 'app:parts:find',
    description: 'Command to search parts and their images in the internet',
)]
class PartsNumbersSearchCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private BrandRepository $brandRepository;
    private CarModelRepository $carModelRepository;
    private PartNameRepository $partNameRepository;
    private PartRepository $partRepository;
    private ClientInterface $client;
    private CacheItemPoolInterface $cache;
    private LockFactory $lockFactory;

    private SymfonyStyle $io;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $brandRepository,
        CarModelRepository $carModelRepository,
        PartNameRepository $partNameRepository,
        PartRepository $partRepository,
        ClientInterface $parserClient,
        CacheItemPoolInterface $dbCache,
        LockFactory $lockFactory,
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
    }

    protected function configure(): void
    {
        $this
            ->addArgument('brand', InputArgument::OPTIONAL, 'Which brand to parse. Use brand name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $brand = $this->getBrand($input);
        if ($brand) {
            $this->io->title("Searching parts for single brand {$brand->getName()}");
            $models = $this->carModelRepository->findAllToParseByBrand($brand);

            $this->processModels($models);
        } else {
            $brands = $this->brandRepository->findAllToParseParts();
            $this->io->title('Searching parts for all brands: '.count($brands));

            foreach ($brands as $brand) {
                $this->io->writeln("Searching parts for brand {$brand->getName()}");

                $models = $this->carModelRepository->findAllToParseByBrand($brand);

                $this->processModels($models);

                $this->io->writeln("Done searching parts for brand {$brand->getName()}. Clearing entity manager");
                $this->entityManager->clear();
                gc_collect_cycles();
            }
        }

        return Command::SUCCESS;
    }

    protected function processModels(array $models)
    {
        $this->io->writeln('Start parsing car models');
        $this->io->progressStart(count($models));
        foreach ($models as $model) {
            $lock = $this->lockFactory->createLock(Locks::PARSING_PARTS_MODEL.$model->getId());
            if (false === $lock->acquire()) {
                $this->io->writeln("Lock is already acquired. Skipping model {$model->getName()}");
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
        $brand = $model->getBrand();
        $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications");
        $modificationsResponse = json_decode($res->getBody()->getContents(), true);

        foreach ($modificationsResponse['modifications'] as $rawModificationData) {
            $alreadyProcessed = $this->cache->getItem("modification_{$rawModificationData['id']}");
            if ($alreadyProcessed->isHit() && true === $alreadyProcessed->get()) {
                dump("Modification {$rawModificationData['id']} already processed");
                continue;
            }
            $nodesRes = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications/{$rawModificationData['id']}/nodes");

            $nodesResponse = json_decode($nodesRes->getBody()->getContents(), true);
            foreach ($nodesResponse['nodes'] as $rawNodeData) {
                $this->processNode($rawNodeData, $rawModificationData['id'], $model);
            }
            $alreadyProcessed->set(true);
            $this->cache->save($alreadyProcessed);
        }
        $model->setModifications($modificationsResponse['modifications']);
        $model->setChildrenPartsParsed(true);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function processNode(array $rawNodeData, int $modificationId, CarModel $model)
    {
        $lock = $this->lockFactory->createLock(Locks::PARSING_PARTS_NODE.$rawNodeData['id'].'_'.$modificationId);
        if (false === $lock->acquire()) {
            $this->io->writeln("Lock is already acquired. Skipping node {$rawNodeData['id']}");

            return;
        }
        foreach ($rawNodeData['children'] as $rawNodeData) {
            $this->processNode($rawNodeData, $modificationId, $model);
        }

        $id = $rawNodeData['id'];
        $alreadyProcessed = $this->cache->getItem("node_{$modificationId}_{$id}");
        if ($alreadyProcessed->isHit()) {
            dump("Node {$modificationId}_{$id} already processed");

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
                $this->entityManager->flush();
            }
        } catch (ServerException $e) {
            dump($e->getMessage());

            $item = $this->cache->getItem('failed_nodes_'.$brand->getName());
            $value = $item->get() ?? [];
            $value[] = $modificationId.'|'.$id;
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
        $part = $this->partRepository->findOneBy(['partNumber' => $partNumber]);
        if (!$part) {
            $part = new Part($partNumber, $partName, $brand);
            if (!empty($images)) {
                $part->setImagesToParse($images);
            }
            $this->entityManager->persist($part);
        }
        $part->addSuitableModel($model);

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
