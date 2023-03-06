<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Form\Request\PartImageType;
use App\Entity\Part;
use App\Service\Image\ImageCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminApiController extends AbstractController
{
    private const IMAGE_FIELD = 'imageAddress';

    #[Route(
        '/admin/api/parts/{id}/images',
        methods: ['POST'],
    )]
    public function downloadImage(
        Request $request,
        Part $part,
        ImageCreator $creator,
    ): ?FormInterface {
        $form = $this->createForm(PartImageType::class);
        $form->submit($request->request->get(self::IMAGE_FIELD));
        if (!$form->isValid()) {
            return $form;
        }

        $creator->createImage($part, $this->getUser(), $form->getData());

        return null;
    }
}
