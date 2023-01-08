<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StaffUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User findOneBy(array $criteria, array $orderBy = null)
 * @method User find($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
