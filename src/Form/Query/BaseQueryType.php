<?php

declare(strict_types=1);

namespace App\Form\Query;

use App\Model\Query\BaseQueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class BaseQueryType extends AbstractType
{
    public const MAX_LIMIT = 20;
    public const MAX_OFFSET = PHP_INT_MAX; // TODO into separate class with constraints

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('limit', NumberType::class, [
                'constraints' => [
                    new Range(['min' => 1, 'max' => self::MAX_LIMIT]),
                ],
            ])
            ->add('offset', NumberType::class, [
                'constraints' => [
                    new Range(['min' => 0, 'max' => self::MAX_OFFSET]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => BaseQueryInterface::class,
        ]);
    }
}
