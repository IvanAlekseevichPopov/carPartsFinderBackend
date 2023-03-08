<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CarModelAdmin extends AppAbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'disabled' => true,
            ])
            ->add('externalId', TextType::class, [
                'disabled' => true,
            ])
            ->add('name')
            ->add('brand')
            ->add('childrenPartsParsed');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('externalId')
            ->add('name')
            ->add(
                'brand',
                null,
                [
                    'field_options' => [
                        'choice_label' => 'name',
                    ],
                ],
            );
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('externalId')
            ->add('name')
            ->add('brand', null, [
                'associated_property' => 'name',
                'admin_code' => 'admin.brand',
                'route' => [
                    'name' => 'edit',
                ],
            ])
            ->add('childrenPartsParsed')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }
}
