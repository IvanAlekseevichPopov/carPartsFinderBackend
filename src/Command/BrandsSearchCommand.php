<?php

namespace App\Command;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:brands:find',
    description: 'Command to search card manufacturers brands',
)]
class BrandsSearchCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private BrandRepository $brandRepository;

    private ClientInterface $client;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository $brandRepository,
        ClientInterface $client,
        string $name = null
    ) {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
        $this->client = $client;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws GuzzleException
     * @throws \Doctrine\ORM\NoResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Start parsing brands');

        $res = $this->client->get('https://tecdoc.autodoc.ru/api/catalogs/tecdoc/brands');
        $arr = json_decode($res->getBody()->getContents(), true);

        $io->progressStart(count($arr));
        if (count($arr['brands']) <= $this->brandRepository->getCount()) {
            $io->success('Brands already parsed');

            return Command::SUCCESS;
        }
        foreach ($arr['brands'] as $item) {
            $brand = new Brand($item['name'], $item['id']);
            $this->entityManager->persist($brand);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->title('Saving brands');
        $this->entityManager->flush();
        $io->success('Brands saved');

        $io->success('Brands parsed');

        return Command::SUCCESS;
    }
}
