<?php

declare(strict_types=1);

namespace App\Service\Guzzle;

use GuzzleHttp\Client;

class ParserClientFactory
{
    private const TIMEOUT = 10;

    public static function create(string $baseUrl, string $referer): Client
    {
        return new Client([
            'base_uri' => $baseUrl,
            'timeout' => self::TIMEOUT,
            'headers' => self::selectRandomHeadersCollection($referer),
        ]);
    }

    private static function selectRandomHeadersCollection(string $referer): array
    {
        switch (random_int(1, 2)) {
            case 1:
                return [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en,ru;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Origin' => $referer,
                    'Referer' => $referer.'/',
                    'Sec-ch-ua' => '"Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"',
                    'Sec-ch-ua-mobile' => '?0',
                    'Sec-fetch-dest' => 'empty',
                    'Sec-fetch-mode' => 'cors',
                    'Sec-fetch-site' => 'same-site',
                ];
            case 2:
                return [
                    'User-Agent' => 'Opera/8.24 (X11; Linux i686; en-US) Presto/2.11.273 Version/10.00',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en,ru;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Origin' => $referer,
                    'Referer' => $referer.'/',
                    'Sec-ch-ua' => 'Sec-CH-UA: "Opera";v="10", " Not;A Brand";v="69", "Chromium";v="5"',
                    'Sec-ch-ua-mobile' => '?0',
                    'Sec-fetch-dest' => 'empty',
                    'Sec-fetch-mode' => 'cors',
                    'Sec-fetch-site' => 'same-site',
                ];
            case 3:
                return [
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en,ru;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Origin' => $referer,
                    'Referer' => $referer.'/',
                    'Sec-ch-ua' => 'Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
                    'sec-ch-ua-platform' => "Linux",
                    'Sec-ch-ua-mobile' => '?0',
                    'Sec-fetch-dest' => 'empty',
                    'Sec-fetch-mode' => 'cors',
                    'Sec-fetch-site' => 'same-site',
                ];
        }
        throw new \LogicException('No headers collection selected');
    }
}
