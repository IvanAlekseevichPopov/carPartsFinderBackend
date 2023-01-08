<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

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

    /**
     * @ORM\Column
     */
    protected string $localPath;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Part", inversedBy="images")
     */
    protected Part $part;

    public function __construct(Part $part, string $localPath)
    {
        $this->localPath = $localPath;
        $this->id = Uuid::uuid7()->toString();
        $this->part = $part;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function getPart(): Part
    {
        return $this->part;
    }
}
