<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="App\Repository\PartRepository")
 */
class Part
{

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected string $id;

    /**
     * @ORM\Column
     */
    protected string $partNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartName")
     */
    protected PartName $partName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     */
    protected Manufacturer $manufacturer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="part")
     */
    protected Collection $images;

    public function __construct(string $partNumber, PartName $partName, Manufacturer $manufacturer)
    {
        $this->partNumber = $partNumber;
        $this->partName = $partName;
        $this->manufacturer = $manufacturer;
        $this->id = Uuid::uuid7()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPartNumber(): string
    {
        return $this->partNumber;
    }

    public function getPartName(): PartName
    {
        return $this->partName;
    }
}
