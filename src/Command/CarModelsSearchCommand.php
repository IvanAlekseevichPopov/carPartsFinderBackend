<?php

namespace App\Command;

use App\Entity\CarModel;
use App\Repository\BrandRepository;
use App\Repository\CarModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:car:find',
    description: 'Command to search car models',
)]
class CarModelsSearchCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private BrandRepository $brandRepository;
    private ClientInterface $client;
    private CacheItemPoolInterface $cache;
    private CarModelRepository $carModelRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $brandRepository,
        CarModelRepository $carModelRepository,
        ClientInterface $client,
        CacheItemPoolInterface $dbCache,
        string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
        $this->client = $client;
        $this->cache = $dbCache;
        $this->carModelRepository = $carModelRepository;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $brands = $this->brandRepository->findAll();
        if (0 === count($brands)) {
            $io->writeln('All car models are parsed');

            return Command::SUCCESS;
        }

        $io->writeln('Start parsing car models');
        $io->progressStart(count($brands));
        foreach ($brands as $brand) {
            $isModelsOfBrandParsed = $this->cache->getItem("brand_{$brand->getExternalId()}_models_parsed");
            if ($isModelsOfBrandParsed->isHit()) {
                $io->progressAdvance();
                $io->writeln(" | {$brand->getName()} already parsed");
                continue;
            }
            $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models");
            $modelsResponse = json_decode($res->getBody()->getContents(), true);

            foreach ($modelsResponse['models'] as $rawModelData) {
                if ($this->carModelRepository->findOneBy(['externalId' => $rawModelData['id']])) {
                    $io->writeln("Model {$rawModelData['name']} already exist in db");
                    continue;
                }
                $carModel = new CarModel($brand, $rawModelData['name'], $rawModelData['id']);
                if (!empty($rawModelData['yearFrom'])) {
                    $carModel->setProductionStart(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearFrom'].'01'));
                }
                if (!empty($rawModelData['yearTo'])) {
                    $carModel->setProductionFinish(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearTo'].'01'));
                }
                $this->entityManager->persist($carModel);
            }

            $isModelsOfBrandParsed->set(true);
            $this->cache->save($isModelsOfBrandParsed);

            $this->entityManager->flush();
            $io->progressAdvance();
            $io->writeln(" | {$brand->getName()} filled: ".count($modelsResponse['models']));

            self::randomWait($io);
        }
        $io->progressFinish();

        return Command::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    protected static function randomWait(SymfonyStyle $io): void
    {
        $time = random_int(1, 4);
        $io->writeln("Waiting {$time} seconds");
        sleep($time);
    }
}
