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

    #[ORM\Column(type: 'json', options: ['jsonb' => true])]
    protected array $suitableForModels = []; // TODO remove if not needed after parsing is finished

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $imagesToParse; // TODO remove after parsing is finished

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

    public function addSuitableModel(CarModel $model): void
    {
        if (in_array($model->getId(), $this->suitableForModels)) {
            return;
        }
        $this->suitableForModels[] = $model->getId();
    }

    public function setImagesToParse(array $images)
    {
        $this->imagesToParse = $images;
    }

    public function getImagesToParse(): ?array
    {
        return $this->imagesToParse;
    }
}
