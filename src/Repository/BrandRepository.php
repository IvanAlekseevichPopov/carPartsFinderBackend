<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Brand findOneBy(array $criteria, array $orderBy = null)
 * @method Brand find($id, $lockMode = null, $lockVersion = null)
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    /**
     * @return Brand[]
     */
    public function findAllToParse(): array
    {
        $qb = $this->createQueryBuilder('brand');

        return $qb
//            ->where('manufacturer.id != 17') // todo remove. it's HONDA
            ->getQuery()
            ->getResult();
    }
}
