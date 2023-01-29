<?php

declare(strict_types=1);

namespace App\Controller\Form\Request;

use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class PartImageType extends UrlType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(),
                new Url(),
            ],
        ]);
    }
}
