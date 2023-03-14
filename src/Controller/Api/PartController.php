<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DBAL\Types\Enum\ViewTypeEnum;
use App\Entity\File\Image;
use App\Entity\File\PartImage;
use App\Entity\Part;
use App\Form\Query\BaseQueryType;
use App\Model\Query\BaseQuery;
use App\Model\Query\PartImageQuery;
use App\Model\View\PartView;
use App\Repository\PartImageRepository;
use App\Repository\PartRepository;
use App\ViewFactory\ImageViewFactory;
use App\ViewFactory\PartViewFactory;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
            return $form; // TODO exception
        }

        $partsList = $partRepository->findByQuery($query);

        return $viewFactory->createListView($partsList);
    }

    // TODO remove???? what about manufacturer?
    #[Route('/api/parts/{id}')]
    public function getOnePart(
        Part $part,
        PartViewFactory $viewFactory,
    ): PartView {
        return $viewFactory->creatSingleView($part);
    }

    #[Route('/api/parts/{id}/images')]
    public function getPartImages(
        Part $part,
        Request $request,
        PartImageRepository $partImageRepository,
        ImageViewFactory $viewFactory,
    ): array|FormInterface {
        $query = new PartImageQuery($part);

        $form = $this->createForm(BaseQueryType::class, $query);
        $form->submit($request->query->all(), false);
        if (!$form->isValid()) {
            return $form; // TODO exception
        }

//        $imageList = $partImageRepository->findByQuery($query);

//        return $viewFactory->createListView($imageList);
        return $viewFactory->createLegacyListView($part);
    }

    #[Route('/api/files/{id}', name: 'api_download_image', methods: ['GET'])]
    public function getImage(PartImage $image, FilesystemOperator $operator, LoggerInterface $logger): Response
    {
        try {
//           TODO checksum сравнение и логгирование несовпадений
//            dump(
//                $operator->checksum($image->getStorageFilePath()),
//                $image->getCheckSum()
//            );
            // TODO content type from image data or always png
            // TODO resize as rcorp??
            return new Response(
                $operator->read($image->getStorageFilePath()),
                Response::HTTP_OK,
                ['Content-Type' => 'image/png', 'max-age' => 10800] // 3 hours
            );
        } catch (FilesystemException $e) {
            $logger->warning("Unable to download image: {$image->getId()}", ['exception' => $e]);

            return new BinaryFileResponse(
                $this->getParameter('kernel.project_dir').'/public/app/img/404.png',
                Response::HTTP_OK,
                ['Content-Type' => 'image/png', 'max-age' => 300] // 5 min
            );
        }
    }
}
