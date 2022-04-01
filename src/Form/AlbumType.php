<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class AlbumType extends AbstractType
{

    private $translator;
    private $date;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->date = new \DateTime('now');
        $this->date = $this->date->format('Y');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => $this->translator->trans('Album Title'),
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('year', ChoiceType::class,[
                'label' => $this->translator->trans('Year of production'),
                'required' => false,
                'choices' => $this->getYears(1951),
                //'data' => $this->date,
                'placeholder' => false,
                'attr' => []
            ])
            ->add('feat', TextType::class,[
                'label' => $this->translator->trans('Featuring'),
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an image file...'),
                ],
                'help' => 'jpeg, bmp, png, svg '.$this->translator->trans('(recommended ratio : 1, ex : 480x480)'),
            ])
        ;
    }

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') + 50 : $max));
        return array_combine($years, $years);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
