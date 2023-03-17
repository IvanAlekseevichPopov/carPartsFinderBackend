<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Part;
use App\Model\Query\BaseQueryInterface;
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

    public function findByQuery(BaseQueryInterface $query): array
    {
        $qb = $this->createQueryBuilder('part');

        return $qb
            ->setMaxResults($query->getLimit())
            ->setFirstResult($query->getOffset())
            ->getQuery()
            ->getResult();
    }

    public function findByBrandName(string $brandName): iterable
    {
        $qb = $this->createQueryBuilder('part');

        //TODO add flag, that part was parsed
        return $qb
            ->join('part.brand', 'brand')
            ->where($qb->expr()->eq($qb->expr()->lower('brand.name'), ':brandName'))
            ->setParameter('brandName', $brandName)
            ->getQuery()
            ->toIterable();
    }
}
