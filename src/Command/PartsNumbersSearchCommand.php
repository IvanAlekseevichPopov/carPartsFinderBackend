<?php

namespace App\Command;

use App\Service\Crawler\ImageCrawlerInterface;
use App\Service\Crawler\PartNumberCrawlerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Panther\Client;

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

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'part numbers to search')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    //парсер номеров запчастей(emex, exist...)
    //забивание в базу, валидация, дедупликация
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        ;

        $client = Client::createChromeClient();

        $client->request('GET', 'https://www.autodoc.ru/price/533/12205PJ7305'); // Yes, this website is 100% written in JavaScript

        $client->takeScreenshot('screenshot.png');

         $crawler = $client->waitFor('.box-original');

         $output->writeln(
             $crawler->filter('.point.ng-star-inserted')->text()
         );

//        $client->clickLink('Get started');

//        $this->partNumberCrawler->findByPartNumber('SAT ST39680SHJA61');

//        for ($i = 0; $i < 100; ++$i) {
//            sleep(10);
//            $output->writeln("$i. Still working...");
//        }

        return Command::SUCCESS;
    }
}
