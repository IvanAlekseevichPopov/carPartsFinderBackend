<?php

declare(strict_types=1);

namespace App\Entity\File;

use App\Entity\Part;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Table]
#[ORM\Entity]
class PartImage
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    protected User $uploadedBy;

    #[ORM\Column]
    private string $checkSum;

    #[ORM\ManyToOne(targetEntity: Part::class, inversedBy: 'images')]
    protected Part $part;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    protected ?int $rating = null;

    public function __construct(User $uploadedBy, Part $part, string $checkSum)
    {
        $this->id = Uuid::uuid7()->toString();
        $this->uploadedBy = $uploadedBy;
        $this->checkSum = $checkSum;
        $this->part = $part;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStorageFilePath(): string
    {
        return '/tmp/'.$this->getId();
    }

    public function getUploadedBy(): User
    {
        return $this->uploadedBy;
    }

    public function getCheckSum(): string
    {
        return $this->checkSum;
    }
}
