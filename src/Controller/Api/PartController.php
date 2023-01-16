<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\File\Image;
use App\Entity\Part;
use App\Form\Query\BaseQueryType;
use App\Model\Query\BaseQuery;
use App\Model\View\PartView;
use App\Repository\PartRepository;
use App\ViewFactory\PartViewFactory;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartController extends AbstractController
{
    #[Route('/api/parts')]
    public function getPartsList(
        Request $request,
        PartRepository $partRepository,
        PartViewFactory $viewFactory,
    ): array|FormInterface {
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
        Part $part,
        PartViewFactory $viewFactory,
    ): PartView {
        return $viewFactory->creatSingleView($part, ViewTypeEnum::DETAILED_ITEM);
    }

    #[Route('/api/images/{id}', name: 'api_download_image', methods: ['GET'])]
    public function getImage(Image $image, FilesystemOperator $operator): Response
    {
        try {
            // TODO content type from image data or always png
            // TODO resize as rcorp??
            return new Response($operator->read($image->getStorageFilePath()), Response::HTTP_OK, ['Content-Type' => 'image/png']);
        } catch (FilesystemException $e) {
            // TODO вернуть image заглушку с коротким ttl и critical в лог
            return new Response('Image not found', Response::HTTP_NOT_FOUND);
        }
    }
}
