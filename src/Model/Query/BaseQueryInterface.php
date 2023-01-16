<?php

declare(strict_types=1);

namespace App\Model\Query;

interface BaseQueryInterface
{
    public const DEFAULT_LIMIT = 10;
    public const DEFAULT_OFFSET = 0;

    public function getLimit(): int;

    public function getOffset(): int;
}
