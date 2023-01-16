<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\File\PartImage;
use App\Model\Query\PartImageQueryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PartImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartImage::class);
    }

    // TODO sort types, order by addition or rating
    public function findByQuery(PartImageQueryInterface $query): array
    {
        $qb = $this->createQueryBuilder('image');

        return $qb
            ->join('image.part', 'part')
            ->where('part = :part')
            ->setParameter('part', $query->getPart())
            ->setMaxResults($query->getLimit())
            ->setFirstResult($query->getOffset())
            ->getQuery()
            ->getResult();
    }
}
