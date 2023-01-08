<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Part;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Part findOneBy(array $criteria, array $orderBy = null)
 * @method Part find($id, $lockMode = null, $lockVersion = null)
 */
class PartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Part::class);
    }
}
