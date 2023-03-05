<?php

declare(strict_types=1);

namespace App\Form\DataMapper;

use App\Entity\File\PartImage;
use App\Entity\Part;
use App\Entity\User;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PartDataMapper implements DataMapperInterface
{
    private TokenStorageInterface $tokenStorage;
    private FilesystemOperator $operator;

    public function __construct(
         TokenStorageInterface $tokenStorage,
         FilesystemOperator $operator
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->operator = $operator;
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {
        if (null !== $viewData) {
            $forms = iterator_to_array($forms);
            $forms['id']->setData($viewData->getId());
            $forms['partNumber']->setData($viewData->getPartNumber());
//            $forms['partName']->setData($viewData->getPartName());
//            $forms['manufacturer']->setData($viewData->getManufacturer());
            $forms['images']->setData($viewData->getImages()->toArray());
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData)
    {
        $forms = iterator_to_array($forms);
        if (!$viewData instanceof Part) {
            throw new \LogicException("Can't handle not Part object");
        }

//        $viewData->getId()
//        $viewData->setPartNumber($forms['partNumber']->getData());
//        $viewData->setPartName($forms['partName']->getData());
        $this->fillImages($forms['images']->getData(), $viewData);
    }

    private function fillImages(array $uploadedImages, Part $viewData)
    {
        if (0 === count($uploadedImages)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new \LogicException("Can't get token");
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new \LogicException("Can't get user");
        }

        foreach ($uploadedImages as $uploadedFile) {
            $image = new PartImage($user, $viewData, md5_file($uploadedFile->getRealPath()));

            // TODO async with message bus. Problem with delition uploadedFile from /tmp
            $this->operator->write($image->getStorageFilePath(), $uploadedFile->getContent());
        }
    }
}
