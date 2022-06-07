<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Tag;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'required' => true,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Name of the event'),
            ])
            ->add('datetime', DateTimeType::class,[
                'required' => false,
                'label' => $this->translator->trans('Enter the date and time'),
                'attr' => [
                    'class' => 'input has-text-centered',
                    'type' => 'datetime',
                ],
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy  HH:mm',
                'help' => $this->translator->trans('Enter the date and time at format : dd/MM/YYYY hh:mm'),
            ])
            ->add('imageFile', FileType::class,[
                'label' => $this->translator->trans('Image'),
                'attr' => [
                    'class' => 'file-input',
                    //'onchange' => 'this.parentElement.parentElement.querySelector(".file-name").innerText = this.files[0].name;',
                    'data-extra' => $this->translator->trans('Select an image file...')
                ],
                'required' => false,
                'help' => 'jpeg, bmp, png, svg '.$this->translator->trans('(recommended ratio : 1/3, ex: 720x240)'),
            ])
            ->add('country', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Country'),
            ])
            ->add('city', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('City'),
            ])
            ->add('cp', TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('ZIP code'),
            ])
            ->add('street', TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Name of the street'),
            ])
            ->add('ticketingLink', TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Redirect link for tickets'),
            ])
            ->add('description', CKEditorType::class,[
                'config' => ['toolbar' => 'standard'],
                'required' => false,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans('Description'),
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
            'data_class' => Event::class,
        ]);
    }
}
