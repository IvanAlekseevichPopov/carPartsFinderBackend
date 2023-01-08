<?php

declare(strict_types=1);

namespace App\Model\Query;

interface BaseQueryInterface
{
    public function getLimit(): int;

    public function getOffset(): int;
}
