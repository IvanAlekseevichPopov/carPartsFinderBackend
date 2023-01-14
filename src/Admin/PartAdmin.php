<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Manufacturer;
use App\Entity\PartName;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PartAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class)
            ->add('partNumber', TextType::class)
            ->add('partName', EntityType::class, [
                'class' => PartName::class,
                'choice_label' => 'name',
            ])
            ->add('manufacturer', EntityType::class, [
                'class' => Manufacturer::class,
                'choice_label' => 'name',
            ])
            ->add('images');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('partNumber')
            ->add('partName.name')
            ->add('manufacturer.name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

}
