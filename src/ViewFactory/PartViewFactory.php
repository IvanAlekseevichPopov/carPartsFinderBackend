<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\Entity\Part;
use App\Model\View\PartView;
use Symfony\Component\Asset\Packages;

class PartViewFactory
{
    private Packages $assetsManager;

    public function __construct(Packages $assetsManager)
    {
        $this->assetsManager = $assetsManager;
    }

    public function creatSingleView(Part $part): PartView
    {
        $view = new PartView();

        $view->id = $part->getId();
        $view->partNumber = $part->getPartNumber();
        $view->manufacturer = $part->getBrand()->getName(); // TODO заменить на id + словарный метод для производителей
        $view->name = $part->getPartName()->getName(); // TODO translate to russian. English name is default and stored in DB
        $view->previewImage = $this->getDraftImagePreview($part);

        return $view;
    }

    public function createListView(array $parts): array
    {
        return array_map([$this, 'creatSingleView'], $parts);
    }

    private function getDraftImagePreview(Part $part)
    {
        $images = $part->getImagesToParse();
        if(!empty($images)) {
            return $images[0];
        }

        return $this->assetsManager->getUrl('app/img/404.png');
        //TODO log that part has no images in database
    }
}
