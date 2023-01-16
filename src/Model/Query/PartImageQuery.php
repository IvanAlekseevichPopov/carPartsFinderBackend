<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Entity\Part;

class PartImageQuery extends BaseQuery implements PartImageQueryInterface
{
    protected Part $part;

    public function __construct(Part $part)
    {
        $this->part = $part;
    }

    public function getPart(): Part
    {
        return $this->part;
    }
}
