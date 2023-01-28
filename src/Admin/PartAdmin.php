<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Brand;
use App\Entity\Part;
use App\Entity\PartName;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\RouterInterface;

class PartAdmin extends AbstractAdmin
{
    private const IMAGES_HTML_CLASS = 'show-images';

    protected RouterInterface $router;
    private DataMapperInterface $dataMapper;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setDataMapper(DataMapperInterface $dataMapper): void
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * This method helps draw images below "upload" button right in the form.
     */
    protected function getImagesCollectionOptions(): array
    {
        $fileFieldOptions = [
            'multiple' => true,
            'required' => false,
            'attr' => ['class' => self::IMAGES_HTML_CLASS],
        ];

        /** @var Part $part */
        $part = $this->getSubject();
        $images = $part->getImages();
        if (!$images->isEmpty()) {
            $imagesRawSting = '';
            foreach ($images as $image) {
                $fullPath = $this->router->generate('api_download_image', ['id' => $image->getId()]);
                $imagesRawSting .= $fullPath.'|';
            }
            $fileFieldOptions['help'] = $imagesRawSting;
        }

        return $fileFieldOptions;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $fileFieldOptions = $this->getImagesCollectionOptions();

        $form
            ->add('id', TextType::class)
            ->add('partNumber', TextType::class)
            ->add('partName', EntityType::class, [
                'class' => PartName::class,
                'choice_label' => 'name',
            ])
            ->add('manufacturer', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
            ])
            ->add('images', FileType::class, $fileFieldOptions)
        ;

        $form->getFormBuilder()->setDataMapper($this->dataMapper);
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
