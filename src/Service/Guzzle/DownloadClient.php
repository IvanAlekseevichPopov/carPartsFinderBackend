<?php

declare(strict_types=1);

namespace App\Service\Guzzle;

use GuzzleHttp\Client as GuzzleClient;

class DownloadClient extends GuzzleClient implements DownloadClientInterface
{
}
