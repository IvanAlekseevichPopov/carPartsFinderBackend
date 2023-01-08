<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Query\BaseQueryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Part;

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

    public function findByQuery(BaseQueryInterface $query): array
    {
        $qb = $this->createQueryBuilder('part');

        return $qb
            ->setMaxResults($query->getLimit())
            ->setFirstResult($query->getOffset())
            ->getQuery()
            ->getResult();
    }
}
