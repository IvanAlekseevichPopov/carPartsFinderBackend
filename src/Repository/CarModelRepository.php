<?php

declare(strict_types=1);

namespace App\Repository;

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
    public function findAllToParsePartsNumbers(): array
    {
        $qb = $this->createQueryBuilder('cm');

        return $qb
            ->where($qb->expr()->eq('cm.childrenPartsParsed', ':alreadyParsed'))
            ->andWhere($qb->expr()->eq('cm.id', ':id'))
            ->setParameter('alreadyParsed', false)
            ->setParameter('id', 2329)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
