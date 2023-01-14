<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table
 * @ORM\Entity()
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected string $id;

//    /**
//     * @ORM\Column
//     */
//    protected string $localPath;
//
//    /**
//     * @ORM\Column(type="smallint", nullable=true)
//     */
//    protected ?int $rating = null;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="App\Entity\Part", inversedBy="images")
//     */
//    protected Part $part;

    protected ?UploadedFile $file = null;

//    public function __construct(Part $part, string $localPath)
    public function __construct()
    {
//        $this->localPath = $localPath;
        $this->id = Uuid::uuid7()->toString();
//        $this->part = $part;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilePath(): string
    {
        return '/tmp/' . $this->getId();
    }
//
//    public function getLocalPath(): string
//    {
//        return $this->localPath;
//    }
//
//    public function getPart(): Part
//    {
//        return $this->part;
//    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }
//
//    public function getRating(): ?int
//    {
//        return $this->rating;
//    }
//
//    public function setRating(?int $rating): void
//    {
//        $this->rating = $rating;
//    }
}
