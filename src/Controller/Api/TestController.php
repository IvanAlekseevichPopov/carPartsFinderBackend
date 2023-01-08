<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Form\Query\BaseQueryType;
use App\Model\Query\BaseQuery;
use App\Repository\PartRepository;
use App\ViewFactory\PartViewFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/parts')]
    public function index(
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
}
