<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\CarModel;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
    private SymfonyStyle $io;

    public function __construct(
        EntityManagerInterface $entityManager,
        BrandRepository        $manufacturerRepository,
        string                 $name = null
    )
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->brandRepository = $manufacturerRepository;
    }

    /**
     * @return string
     */
    public function getRandomUserAgent(): string
    {
        switch (random_int(0, 4)) {
            case 0:
                return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
            case 1:
                return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0';
            case 2:
                return 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15';
            case 3:
                return 'Opera/9.96 (X11; Linux x86_64; sl-SI) Presto/2.12.343 Version/11.00';
            case 4:
                return 'Mozilla/5.0 (Windows NT 6.1; sl-SI; rv:1.9.2.20) Gecko/20171125 Firefox/37.0';
        }
        return 'Mozilla/5.0 (Windows 98) AppleWebKit/5311 (KHTML, like Gecko) Chrome/37.0.840.0 Mobile Safari/5311';

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
        $this->parseBrands();
        $this->parseCarModels();

        return Command::SUCCESS;
    }

    private function parseBrands()
    {
        $this->io->title('Parsing brands');
        $client = new Client();
        $res = $client->request('GET', 'https://tecdoc.autodoc.ru/api/catalogs/tecdoc/brands');
        $arr = json_decode($res->getBody()->getContents(), true);

        $this->io->progressStart(count($arr));
        foreach ($arr['brands'] as $item) {
            $brand = new Brand($item['name'], $item['id']);
            $this->entityManager->persist($brand);
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();
        $this->io->title('Saving brands');
        $this->entityManager->flush();
        $this->io->success('Brands saved');

        $this->io->success('Brands parsed');
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    private function parseCarModels()
    {
        $brands = $this->brandRepository->findAllToParseModels();
        $client = new Client();

        $this->io->writeln('Start parsing car models');
        $this->io->progressStart(count($brands));
        foreach ($brands as $brand) {
            try {
                $res = $client->request(
                    'GET',
                    "https://tecdoc.autodoc.ru/api/catalogs/tecdoc/brands/{$brand->getExternalId()}/models",
                    [
                        'headers' => [
                            'User-Agent' => $this->getRandomUserAgent(),
                            'Accept-Language' => 'en,ru;q=0.9',
                            'referer' => 'https://www.autodoc.ru/',
//                            'sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
                        ]
                    ]);
                $modelsResponse = json_decode($res->getBody()->getContents(), true);

                foreach ($modelsResponse['models'] as $rawModelData) {
                    $carModel = new CarModel($brand, $rawModelData['name'], $rawModelData['id']);
                    if(!empty($rawModelData['yearFrom'])) {
                        $carModel->setProductionStart(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearFrom'] . '01'));
                    }
                    if (!empty($rawModelData['yearTo'])) {
                        $carModel->setProductionFinish(\DateTimeImmutable::createFromFormat('Ymd', $rawModelData['yearTo'] . '01'));
                    }
                    $this->entityManager->persist($carModel);
                }

                $brand->setChildrenModelsParsed(true);
                $this->entityManager->flush();
                $this->io->progressAdvance();
                $this->io->writeln(" | {$brand->getName()} filled: " .count($modelsResponse['models']));

                $time = random_int(1, 4);
                $this->io->writeln("Waiting {$time} seconds");
                sleep($time);
            } catch (\Throwable $exception) {
                dump($modelsResponse, $rawModelData) ;
                throw $exception;
            }
        }

        $this->io->progressFinish();
    }
}

//    //забивание в базу, валидация, дедупликация
//    protected function execute(InputInterface $input, OutputInterface $output): int
//    {
//
//        //TODO создать реалистичные конфигурации компьютеров,
//        // которые будут рандомно загружаться фабрикой клиентов
//
//
//        //TODO
//
// //        $client = Client::createChromeClient();
//        $client = Client::createChromeClient(null, ['--headless', '--disable-dev-shm-usage', '--no-sandbox'], ['chromedriver_arguments' => ['--log-path=var/log/chrome.log', '--log-level=INFO']]);
//        $size = new WebDriverDimension(1024, 768);
//        $client->manage()->window()->setSize($size);
//        dump(1);
//
//
//        //номер запчасти
//
//        $client->request('GET', 'https://www.autodoc.ru/price/533/12205PJ7305'); // Yes, this website is 100% written in JavaScript
// //        $client->request('GET', 'https://api-platform.com'); // Yes, this website is 100% written in JavaScript
//        dump(2);
//
//        $client->wait(3, 0);
//        $client->takeScreenshot('screenshot.png');
//        dump(3);
//
//         $crawler = $client->waitFor('.detail', 3);
//            dump(4);
//
//
//            dump($crawler->filter('div.detail')->filter('span.right')->eq(1)->getText());
//
// //         $output->writeln(
// //             $crawler->filter('.point.ng-star-inserted')->text()
// //         );
//
// //        $client->clickLink('Get started');
//
// //        $this->partNumberCrawler->findByPartNumber('SAT ST39680SHJA61');
//
// //        for ($i = 0; $i < 100; ++$i) {
// //            sleep(10);
// //            $output->writeln("$i. Still working...");
// //        }
//
//        return Command::SUCCESS;
//    }
