<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Manufacturer;
use App\Entity\Part;
use App\Model\Query\BaseQueryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Manufacturer findOneBy(array $criteria, array $orderBy = null)
 * @method Manufacturer find($id, $lockMode = null, $lockVersion = null)
 */
class ManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manufacturer::class);
    }
}
