<?php

namespace App\Form;

use App\Entity\Book;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookType extends AbstractType
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
                'label' => $this->translator->trans($this->translator->trans('Title')),
            ])

            ->add('description', CKEditorType::class,[
                'config' => ['toolbar' => 'standard'],
                'required' => false,
                'attr' => [
                    'class' => 'textarea'
                ],
                'label' => $this->translator->trans($this->translator->trans('Description')),
            ])

            ->add('redirect_link', TextType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'input'
                ],
                'label' => $this->translator->trans('Redirect link for get the book'),
            ])

            ->add('tags', TextType::class,[
                'required'=> false,
//                'label' => $this->translator->trans('Tags'),
                'attr' => [
                    'class' => 'tags-input',
                    'data-type' => 'tags',
                    'placeholder' => $this->translator->trans('Add a Tag')
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => $this->translator->trans('Cover Page (Front)'),
                'required' => false,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an image file...'),
                ],
                'help' => 'jpeg, bmp, png, svg '.$this->translator->trans('(recommended ratio : 4/5, ex: 480x600)'),
            ])
            ->add('imageVersoFile', FileType::class, [
                'label' => $this->translator->trans('Back of the book'),
                'required' => false,
                'attr' => [
                    'class' => 'file-input',
                    'extra-data' => $this->translator->trans('Select an image file...'),
                ],
                'help' => 'jpeg, bmp, png, svg '.$this->translator->trans('(recommended ratio : 4/5, ex: 480x600)'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
