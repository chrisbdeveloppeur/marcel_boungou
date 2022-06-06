<?php

namespace App\Form;

use App\Entity\News;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewsType extends AbstractType
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
                'help' => $this->translator->trans('The title is required'),
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('New\'s title'),
            ])
            ->add('description', CKEditorType::class,[
                'config' => ['toolbar' => 'full'],
                'help' => $this->translator->trans('The french content is required'),
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans('New\'s content'),
            ])
            ->add('descriptionEn', CKEditorType::class,[
                'config' => ['toolbar' => 'full'],
                'required' => false,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans('Content in English language'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
