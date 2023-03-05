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
use DateTimeImmutable;
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
    name: 'app:car:find',
    description: 'Command to search car models',
)]
class CarModelsSearchCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private BrandRepository $brandRepository;

    private SymfonyStyle $io;
    private Client $client;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $manufacturerRepository,
        Client $client,
        string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $manufacturerRepository;
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

        $brands = $this->brandRepository->findAllToParseModels();

        $this->io->writeln('Start parsing car models');
        $this->io->progressStart(count($brands));
        foreach ($brands as $brand) {
            $res = $this->client->get("/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models");
            $modelsResponse = json_decode($res->getBody()->getContents(), true);

            foreach ($modelsResponse['models'] as $rawModelData) {
                $carModel = new CarModel($brand, $rawModelData['name'], $rawModelData['id']);
                if (!empty($rawModelData['yearFrom'])) {
                    $carModel->setProductionStart(DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearFrom'].'01'));
                }
                if (!empty($rawModelData['yearTo'])) {
                    $carModel->setProductionFinish(DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearTo'].'01'));
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


        return Command::SUCCESS;
    }
}
