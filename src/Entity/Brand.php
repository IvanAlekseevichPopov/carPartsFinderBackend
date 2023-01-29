<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(
    indexes: [
        new ORM\Index(columns: ['name']),
    ]
)]
#[ORM\Entity(repositoryClass: 'App\Repository\BrandRepository')]
class Brand
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $externalId;

    #[ORM\Column]
    protected string $name; // todo trasnlation

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $childrenModelsParsed = false;

    public function __construct(string $defaultLocaleName, int $externalId)
    {
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

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function isChildrenModelsParsed(): bool
    {
        return $this->childrenModelsParsed;
    }

    public function setChildrenModelsParsed(bool $childrenModelsParsed): void
    {
        $this->childrenModelsParsed = $childrenModelsParsed;
    }
}
