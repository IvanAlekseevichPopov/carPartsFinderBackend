<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\CarModel;
use App\Entity\Part;
use App\Model\Query\BaseQueryInterface;
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

}