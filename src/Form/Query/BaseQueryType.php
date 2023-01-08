<?php

declare(strict_types=1);

namespace App\Form\Query;

use App\Model\Query\BaseQueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;

class BaseQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('limit', NumberType::class, [
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 1]),
                ],
            ])
            ->add('offset', NumberType::class, [
                'constraints' => [
                    new Range(['min' => 0]),
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
