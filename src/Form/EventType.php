<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventType extends AbstractType
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Name of the event'),
            ])
            ->add('datetime', null,[
                'attr' => [
                    'type' => 'date',
                    'class' => 'bulma-calendar bulma-calendar-2'
                ],
                'label' => $this->translator->trans('Select the date'),
            ])
            ->add('country', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Country'),
            ])
            ->add('city', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('City'),
            ])
            ->add('cp', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Post code'),
            ])
            ->add('street', TextType::class,[
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Adresse street'),
            ])
            //->add('ics_file')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
