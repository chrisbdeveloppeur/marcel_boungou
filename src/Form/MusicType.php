<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Music;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;


class MusicType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ]
            ])

            ->add('musicFile', FileType::class, [
                'label' => $this->translator->trans('Audio file'),
                'required' => false,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an audio file...'),
                ],
            ])

            ->add('album', EntityType::class, [
                'class' => Album::class,
                'label' => 'Album',
                'required' => false,
                'attr' => [
                    'class' => 'select',
                ],
                'placeholder' => $this->translator->trans('select an album')
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Music::class,
        ]);
    }
}
