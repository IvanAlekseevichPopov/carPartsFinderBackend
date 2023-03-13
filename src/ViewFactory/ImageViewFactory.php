<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\File\PartImage;
use App\Model\View\ImageView;

class ImageViewFactory
{
    public function createSingleView(PartImage $image): ImageView
    {
        $view = new ImageView();
        $view->id = $image->getId();
        $view->rating = $image->getRating();

        return $view;
    }

    public function createListView(array $images): array
    {
        return array_map([$this, 'createSingleView'], $images);
    }
}
