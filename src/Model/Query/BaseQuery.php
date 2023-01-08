<?php

declare(strict_types=1);

namespace App\Model\Query;

class BaseQuery implements BaseQueryInterface
{
    protected int $limit = 5;

    protected int $offset = 0;

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }
}
