<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\Part;
use App\Model\View\PartDetailedView;
use App\Model\View\PartView;

class PartViewFactory
{
    private ImageViewFactory $imageViewFactory;

    public function __construct(ImageViewFactory $imageViewFactory)
    {
        $this->imageViewFactory = $imageViewFactory;
    }

    private function createBaseView(Part $part, $viewType = ViewTypeEnum::LIST_ITEM): PartView
    {
        if (ViewTypeEnum::DETAILED_ITEM == $viewType) {
            $view = new PartDetailedView();
            $view->images = $this->imageViewFactory->createListView($part->getImages()->toArray());

            return $view;
        }

        $view = new PartView();
        $firstImage = $part->getImages()->first();
        if ($firstImage) {
            $view->previewImage = $firstImage->getId();
        }

        return $view;
    }

    public function creatSingleView(Part $part, ViewTypeEnum $viewType = ViewTypeEnum::LIST_ITEM): PartView
    {
        $view = $this->createBaseView($part, $viewType);

        $view->id = $part->getId();
        $view->partNumber = $part->getPartNumber();
        $view->manufacturer = $part->getBrand()->getName(); // TODO заменить на id + словарный метод для производителей
        $view->name = $part->getPartName()->getName(); // TODO translate to russian. English name is default and stored in DB

        return $view;
    }

    public function createListView(array $parts): array
    {
        return array_map([$this, 'creatSingleView'], $parts);
    }
}
