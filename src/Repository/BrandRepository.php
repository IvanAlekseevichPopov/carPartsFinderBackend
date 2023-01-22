<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\Part;
use App\Model\Query\BaseQueryInterface;
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

    public function findAllToParse(): array
    {
        $qb = $this->createQueryBuilder('manufacturer');

        return $qb
            ->where('manufacturer.id != 17') // todo remove. it's HONDA
            ->getQuery()
            ->getResult();
    }
}
