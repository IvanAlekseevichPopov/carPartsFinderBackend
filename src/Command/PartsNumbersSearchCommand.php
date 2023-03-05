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
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\Cache\Adapter\DoctrineDbalAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
    private DoctrineDbalAdapter $cache;

    private SymfonyStyle $io;
    private Client $client;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $manufacturerRepository,
        CarModelRepository $carModelRepository,
        PartNameRepository $partNameRepository,
        PartRepository $partRepository,
        Client $client,
        DoctrineDbalAdapter $cache,
        string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $manufacturerRepository;
        $this->carModelRepository = $carModelRepository;
        $this->partNameRepository = $partNameRepository;
        $this->partRepository = $partRepository;
        $this->cache = $cache;
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'part numbers to search')//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->parseCarModels();

        $this->parsePartsNumbers();

        return Command::SUCCESS;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    private function parseCarModels()
    {
        $brands = $this->brandRepository->findAllToParseModels();

        $this->io->writeln('Start parsing car models');
        $this->io->progressStart(count($brands));
        foreach ($brands as $brand) {
            $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models");
            $modelsResponse = json_decode($res->getBody()->getContents(), true);

            foreach ($modelsResponse['models'] as $rawModelData) {
                $carModel = new CarModel($brand, $rawModelData['name'], $rawModelData['id']);
                if (!empty($rawModelData['yearFrom'])) {
                    $carModel->setProductionStart(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearFrom'].'01'));
                }
                if (!empty($rawModelData['yearTo'])) {
                    $carModel->setProductionFinish(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearTo'].'01'));
                }
                $this->entityManager->persist($carModel);
            }

            $brand->setChildrenModelsParsed(true);
            $this->entityManager->flush();
            $this->io->progressAdvance();
            $this->io->writeln(" | {$brand->getName()} filled: ".count($modelsResponse['models']));

            $time = random_int(1, 4);
            $this->io->writeln("Waiting {$time} seconds");
            sleep($time);
        }

        $this->io->progressFinish();
    }

    private function parsePartsNumbers()
    {
        $models = $this->carModelRepository->findAllToParsePartsNumbers();
        $this->io->writeln('Start parsing car models');
        $this->io->progressStart(count($models));
        foreach ($models as $model) {
            $brand = $model->getBrand();
            $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models/{$model->getExternalId()}/modifications");
            $modificationsResponse = json_decode($res->getBody()->getContents(), true);

            foreach ($modificationsResponse['modifications'] as $rawModificationData) {
                $alreadyProcessed = $this->cache->getItem("modification_{$rawModificationData['id']}");
                if ($alreadyProcessed->isHit()) {
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
    }

    private function processNode(array $rawNodeData, int $modificationId, CarModel $model)
    {
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
}
