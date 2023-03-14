<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\Entity\File\PartImage;
use App\Entity\Part;
use App\Model\View\ImageView;
use Ramsey\Uuid\Uuid;

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

    public function createLegacyListView(Part $part): array
    {
        $views = [];

        foreach ($part->getImagesToParse() as $path) {
            $view = new ImageView();
            $view->id = Uuid::uuid6()->toString();
            $view->rating = null;
            $view->voicesCount = 0;
            $view->path = $path;

            $views[] = $view;
        }

        return $views;
    }
}
