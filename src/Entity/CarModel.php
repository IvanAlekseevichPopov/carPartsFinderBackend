<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class CarModel
{
    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id;

    /**
     * @ORM\Column
     */
    protected string $name = ''; // todo trasnlation

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="models")
     */
    protected Brand $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
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

    public function getBrand(): Brand
    {
        return $this->brand;
    }
}
