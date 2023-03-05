<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\CarModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarModel findOneBy(array $criteria, array $orderBy = null)
 * @method CarModel find($id, $lockMode = null, $lockVersion = null)
 */
class CarModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarModel::class);
    }

    /**
     * @return CarModel[]
     */
    public function findAllToParseByBrand(Brand $brand): array
    {
        $qb = $this->createQueryBuilder('cm');

        return $qb
            ->join('cm.brand', 'b')
            ->where($qb->expr()->eq('cm.childrenPartsParsed', ':alreadyParsed'))
            ->andWhere($qb->expr()->eq('b.id', ':brand'))
            ->setParameter('alreadyParsed', false)
            ->setParameter('brand', $brand)
            ->getQuery()
            ->getResult();
    }
}
