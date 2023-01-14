<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Image;
use League\Flysystem\FilesystemOperator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Range;

class ImageAdmin extends AbstractAdmin
{
    private FilesystemOperator $operator;

    public function setStorage(FilesystemOperator $operator)
    {
        $this->operator = $operator;
    }


    public function prePersist(object $object): void
    {
        parent::prePersist($object);
        if( $object instanceof Image && $object->getFile() !== null) {
            $this->sendImageToStorage($object);
        }
    }

    public function preUpdate(object $object): void
    {
        parent::preUpdate($object);
        if( $object instanceof Image && $object->getFile() !== null) {
            $this->sendImageToStorage($object);
        }

    }

    private function sendImageToStorage(Image $image): void
    {
        dump($image);
        $this->operator->writeStream(
             $image->getFilePath(),
            fopen($image->getFile()->getRealPath(), 'r')
        );
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
//            ->add('rating', NumberType::class, [
//                'constraints' => [
//                    new Range(['min' => 0, 'max' => 500]),
//                ]
//            ])
            ->add('file', FileType::class, [

            ])//            ->add('path', TextType::class)
        ;
//            ->add('partName', EntityType::class, [
//                'class' => PartName::class,
//                'choice_label' => 'name',
//            ])
//            ->add('manufacturer', EntityType::class, [
//                'class' => Manufacturer::class,
//                'choice_label' => 'name',
//            ])
//            ->add('images');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
//            ->add('rating')
//            ->add('partName.name')
//            ->add('manufacturer.name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

}
