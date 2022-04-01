<?php

namespace App\Form;

use App\Entity\Biography;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class BiographyType extends AbstractType
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', CKEditorType::class,[
                'config' => ['toolbar' => 'full'],
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans('Text in French'),
            ])
            ->add('contentEn', CKEditorType::class,[
                'config' => ['toolbar' => 'full'],
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans('Text in English'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Biography::class,
        ]);
    }
}
