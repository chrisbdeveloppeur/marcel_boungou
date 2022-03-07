<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PictureType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                'label' => $this->translator->trans('Image file'),
                'required' => true,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an image file...'),
                ],
            ])
            ->add('title', TextType::class,[
                'required' => false,
                'label' => $this->translator->trans('Picture title'),
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('tags', TextType::class,[
                'required'=> false,
                'label' => false,
                'attr' => [
                    'class' => 'tags-input',
                    'data-type' => 'tags',
                    'placeholder' => $this->translator->trans('Add a Tag')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
