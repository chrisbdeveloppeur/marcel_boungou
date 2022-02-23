<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('year', ChoiceType::class,[
                'choices' => $this->getYears($this->date),
                'mapped' => false,
                'attr' => [
//                    'class' => 'input',
                ]
            ])
            ->add('feat', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ]
            ])
            ->add('imageFile', VichFileType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an image file...'),
                ],
            ])
        ;
    }

    private function getYears($min, $max='current')
    {
        $years = range($min, ($max === 'current' ? date('Y') + 20 : $max));
        return array_combine($years, $years);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
