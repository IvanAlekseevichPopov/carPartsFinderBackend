<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\Part;
use App\Form\Query\BaseQueryType;
use App\Model\Query\BaseQuery;
use App\Model\View\PartView;
use App\Repository\PartRepository;
use App\ViewFactory\PartViewFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PartController extends AbstractController
{
    #[Route('/api/parts')]
    public function getPartsList(
        Request         $request,
        PartRepository  $partRepository,
        PartViewFactory $viewFactory,
    )
    {
        $query = new BaseQuery();

        $form = $this->createForm(BaseQueryType::class, $query);
        $form->submit($request->query->all(), false);
        if (!$form->isValid()) {
            return $form;
        }

        $partsList = $partRepository->findByQuery($query);

        return $viewFactory->createListView($partsList);
    }

    #[Route('/api/parts/{id}')]
    public function getOnePart(
        Part            $part,
        PartViewFactory $viewFactory,
    ): PartView
    {
        return $viewFactory->creatSingleView($part, ViewTypeEnum::DETAILED_ITEM);
    }
}
