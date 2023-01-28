<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    indexes: [
        new ORM\Index(columns: ['name']),
    ]
)]
#[ORM\Entity]
class CarModel
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id;

    #[ORM\Column]
    protected string $name; // todo trasnlation

    #[ORM\ManyToOne(targetEntity: Brand::class)]
    protected Brand $brand;

    #[ORM\Column(type: 'integer', unique: true)]
    protected int $externalId;

    #[ORM\Column(type: 'date_immutable')]
    protected ?\DateTimeImmutable $productionStart = null;

    #[ORM\Column(type: 'date_immutable')]
    protected ?\DateTimeImmutable $productionFinish = null;

    public function __construct(Brand $brand, string $defaultLocaleName, int $externalId)
    {
        $this->brand = $brand;
        $this->name = $defaultLocaleName;
        $this->externalId = $externalId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function getProductionStart(): ?\DateTimeImmutable
    {
        return $this->productionStart;
    }

    public function setProductionStart(?\DateTimeImmutable $productionStart): void
    {
        $this->productionStart = $productionStart;
    }

    public function getProductionFinish(): ?\DateTimeImmutable
    {
        return $this->productionFinish;
    }

    public function setProductionFinish(?\DateTimeImmutable $productionFinish): void
    {
        $this->productionFinish = $productionFinish;
    }
}
