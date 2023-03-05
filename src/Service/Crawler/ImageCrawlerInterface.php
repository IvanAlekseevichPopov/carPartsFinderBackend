<?php

declare(strict_types=1);

namespace App\Service\Crawler;

interface ImageCrawlerInterface
{
    public function findByPartNumber(string $partNumber): array;
}
