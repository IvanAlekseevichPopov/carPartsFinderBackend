<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getCount(): int
    {
        $qb = $this->createQueryBuilder('brand');

        return (int) $qb
            ->select($qb->expr()->count('brand.id'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findWithSimilarName(string $brandName): ?Brand
    {
        $qb = $this->createQueryBuilder('brand');

        $brands = $qb
            ->where($qb->expr()->like($qb->expr()->lower('brand.name'), ':name'))
            ->setParameter('name', '%'.mb_strtolower($brandName).'%')
            ->getQuery()
            ->getResult();

        if (0 === count($brands)) {
            return null;
        }
        if (1 === count($brands)) {
            return $brands[0];
        }

        $qb = $this->createQueryBuilder('brand');

        return $qb
            ->where($qb->expr()->eq($qb->expr()->lower('brand.name'), ':name'))
            ->setParameter('name', mb_strtolower($brandName))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Brand[]
     */
    public function findAllToParseParts(): array
    {
        $qb = $this->createQueryBuilder('brand');

        return $qb
            ->where($qb->expr()->eq('brand.childrenModelsParsed', ':alreadyParsed'))
            ->setParameter('alreadyParsed', false)
            ->getQuery()
            ->getResult();
    }
}
