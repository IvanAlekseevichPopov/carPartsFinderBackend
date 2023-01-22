<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\CarModel;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:parts:find',
    description: 'Command to search parts and their images in the internet',
)]
class PartsNumbersSearchCommand extends Command
{
//    private PartNumberCrawlerInterface $partNumberCrawler;
//
//    public function __construct(PartNumberCrawlerInterface $partNumberCrawler, string $name = null)
//    {
//        parent::__construct($name);
//
//        $this->partNumberCrawler = $partNumberCrawler;
//    }

    private EntityManagerInterface $entityManager;
    private BrandRepository $manufacturerRepository;

    public function __construct(EntityManagerInterface $entityManager, BrandRepository $manufacturerRepository, string $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'part numbers to search')//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $this->parseManufacturers($input, $output)  ;

//        $this->parseCarModels($input, $output);

//        $this->parsePartNumbers($input, $output);


//        dump(json_decode($res->getBody()->getContents()));


        return Command::SUCCESS;
    }

//    //парсер номеров запчастей(emex, exist...)
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
////        $client = Client::createChromeClient();
//        $client = Client::createChromeClient(null, ['--headless', '--disable-dev-shm-usage', '--no-sandbox'], ['chromedriver_arguments' => ['--log-path=var/log/chrome.log', '--log-level=INFO']]);
//        $size = new WebDriverDimension(1024, 768);
//        $client->manage()->window()->setSize($size);
//        dump(1);
//
//
//        //номер запчасти
//
//        $client->request('GET', 'https://www.autodoc.ru/price/533/12205PJ7305'); // Yes, this website is 100% written in JavaScript
////        $client->request('GET', 'https://api-platform.com'); // Yes, this website is 100% written in JavaScript
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
////         $output->writeln(
////             $crawler->filter('.point.ng-star-inserted')->text()
////         );
//
////        $client->clickLink('Get started');
//
////        $this->partNumberCrawler->findByPartNumber('SAT ST39680SHJA61');
//
////        for ($i = 0; $i < 100; ++$i) {
////            sleep(10);
////            $output->writeln("$i. Still working...");
////        }
//
//        return Command::SUCCESS;
//    }
    private function parseManufacturers(InputInterface $input, OutputInterface $output)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->request("GET", "https://webapi.autodoc.ru/api/catalogs/dop/brands");
        $arr = json_decode($res->getBody()->getContents(), true);

        foreach ($arr['items'] as $key => $item) {

            $manufacturer = new Brand();
            $manufacturer->setName($item['catalogName']);
            $this->entityManager->persist($manufacturer);

//            if($key > 4) { //todo remove after tessts
//                break;
//            }
        }

        $this->entityManager->flush();
    }

    private function parseCarModels(InputInterface $input, OutputInterface $output)
    {
        $manufacturers = $this->manufacturerRepository->findAllToParse();
        $client = new \GuzzleHttp\Client();

        dump($manufacturers);
        foreach ($manufacturers as $manufacturer) {
            $res = $client->request("GET", "https://catalogoriginal.autodoc.ru/api/catalogs/original/brands/{$manufacturer->getName()}/models");
            $arr = json_decode($res->getBody()->getContents(), true);

            foreach ($arr as $key => $item) {
//                dump($item);

                $carModel = new CarModel($manufacturer);
                $carModel->setName($item['name']);
                $this->entityManager->persist($carModel);
            }

            $this->entityManager->flush();
            dump("saved");
        }
    }
}
