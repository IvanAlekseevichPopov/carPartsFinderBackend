<?php

declare(strict_types=1);

namespace App\Service\Crawler;

interface PartNumberCrawlerInterface
{
    public function findByPartNumber(string $partNumber): array;
}
