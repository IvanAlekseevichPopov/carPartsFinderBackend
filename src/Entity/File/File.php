<?php

declare(strict_types=1);

namespace App\Entity\File;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="document_type", type="integer")
 * @ORM\DiscriminatorMap({
 *      1 = "App\Entity\File\Image",
 *      2 = "App\Entity\File\PartImage",
 * })
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    protected User $uploadedBy;

    protected ?UploadedFile $file = null;

    public function __construct(User $uploadedBy)
    {
        $this->id = Uuid::uuid7()->toString();
        $this->uploadedBy = $uploadedBy;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStorageFilePath(): string
    {
        return '/tmp/'.$this->getId();
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getUploadedBy(): User
    {
        return $this->uploadedBy;
    }
}
