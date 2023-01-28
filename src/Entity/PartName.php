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
class PartName
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id;

    #[ORM\Column]
    protected string $name; // todo trasnlation

    public function __construct(string $defaultLocaleName)
    {
        $this->name = $defaultLocaleName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
