<?php

declare(strict_types=1);

namespace App\Model\Query;

use App\Entity\Part;

interface PartImageQueryInterface extends BaseQueryInterface
{
    public function getPart(): Part;
}
