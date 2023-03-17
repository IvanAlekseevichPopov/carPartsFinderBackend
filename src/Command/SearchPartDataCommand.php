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
use GuzzleHttp\Client;
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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(
    name: 'app:part-data:search',
    description: 'Command to search photo of parts from mann',
)]
class SearchPartDataCommand extends Command
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

        $client = new Client([
            'base_uri' => 'https://partsfinder.bilsteingroup.com',
        ]);

        $response = $client->get('/en/article/blueprint/ADH22303');

//        dump($response->getBody()->getContents());

        $crawler = new Crawler($response->getBody()->getContents());

        dump($crawler->filterXPath('//body/main/div')->getNode(0)->textContent);
//
//        $crawler->ge

        return Command::SUCCESS;
    }
}
