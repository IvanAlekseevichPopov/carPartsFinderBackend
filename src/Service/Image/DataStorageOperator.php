<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Entity\File\PartImage;
use App\Entity\Part;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;

class DataStorageOperator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function write(Part $part, UserInterface $user, File $file): PartImage
    {
        $entity = new PartImage($user, $part, md5_file($file->getRealPath()));

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(PartImage $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
