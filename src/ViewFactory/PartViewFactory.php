<?php

declare(strict_types=1);

namespace App\ViewFactory;

use App\Entity\Part;
use App\Model\View\PartView;

class PartViewFactory
{
    public function createPartView(Part $part): PartView
    {
        $view = new PartView();

        $view->id = $part->getId();
        $view->partNumber = $part->getPartNumber();
        //TODO partname в виде констант(легко держать переводы в yml) или в БД(но тогда переводы тож надо строить динамически)
        $view->name = $part->getPartName()->getName(); //TODO translate to russian. English name is default and stored in DB
        $view->images = [];//TODO images collection( with path, stars, get by robot or human, etc...)

        return $view;
    }

    public function createListView(array $parts): array
    {
        return array_map([$this, 'createPartView'], $parts);
    }
}