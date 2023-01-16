<?php

declare(strict_types=1);

namespace App\Entity\File;

use App\Entity\Part;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PartImage extends Image
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Part", inversedBy="images")
     */
    protected Part $part;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected ?int $rating = null;

    public function __construct(User $uploadedBy, Part $part)
    {
        parent::__construct($uploadedBy);
        $this->part = $part;
        $part->addImage($this);
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
}
