<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\Part;
use App\Model\View\PartDetailedView;
use App\Model\View\PartView;

class PartViewFactory
{
    private function createBaseView(Part $part, $viewType = ViewTypeEnum::LIST_ITEM): PartView
    {
        if ($viewType == ViewTypeEnum::DETAILED_ITEM) {
            $view = new PartDetailedView();
            $view->images = $part
                ->getImages()
                ->map(fn($image) => $image->getPath())
                ->toArray(); //TODO ограничение количества, может быть очень много

            return $view;
        }

        return new PartView();
    }

    public function creatSingleView(Part $part, ViewTypeEnum $viewType = ViewTypeEnum::LIST_ITEM): PartView
    {
        $view = $this->createBaseView($part, $viewType);

        $view->id = $part->getId();
        $view->partNumber = $part->getPartNumber();
        $view->manufacturer = $part->getManufacturer()->getName(); //TODO заменить на id + словарный метод для производителей
        $view->name = $part->getPartName()->getName(); //TODO translate to russian. English name is default and stored in DB
        $view->previewImage = $part->getImages()->first()->getPath(); //TODO images collection( with path, stars, get by robot or human, etc...)

        return $view;
    }

    public function createListView(array $parts): array
    {
        return array_map([$this, 'creatSingleView'], $parts);
    }
}