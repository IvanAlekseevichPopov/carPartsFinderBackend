<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\File\PartImage;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Table(
    indexes: [
        new ORM\Index(columns: ['part_number']),
    ]
)]
#[ORM\Entity(repositoryClass: 'App\Repository\PartRepository')]
class Part
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected string $id;

    #[ORM\Column]
    protected string $partNumber;

    #[ORM\ManyToOne(targetEntity: PartName::class)]
    protected PartName $partName;

    #[ORM\ManyToOne(targetEntity: Brand::class)]
    protected Brand $brand;

    #[ORM\OneToMany(mappedBy: 'part', targetEntity: PartImage::class, cascade: ['persist', 'remove'])]
    protected Collection $images;

    public function __construct(string $partNumber, PartName $partName, Brand $brand)
    {
        $this->partNumber = $partNumber;
        $this->partName = $partName;
        $this->brand = $brand;
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

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

//    public function addImage(PartImage $image): void
//    {
//        if (!$this->images->contains($image)) {
//            $this->images->add($image);
//        }
//    }
//
//    public function removeImage(PartImage $image): void
//    {
//        $this->images->removeElement($image);
//    }
}
