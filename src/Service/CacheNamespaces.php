<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Cache namespaces.
 * Only characters in [-+.A-Za-z0-9] are allowed
 */
interface CacheNamespaces
{
    public const PARSER_CACHE = 'parser-cache';
}
