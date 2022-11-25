<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sender', TextType::class,[
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'input has-text-centered is-size-5-mobile',
                    'placeholder' => $this->translator->trans( 'Your Email...')
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => "/[&;:<>{}\/]/",
                        'match' => false,
                        'message' => $this->translator->trans('Characters not allowed'),
                    ]),
                ],
            ])
            ->add('subject', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => 'input has-text-centered is-size-5-mobile',
                    'placeholder' => $this->translator->trans( 'The subject of the message...')
                ],
                'label' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => "/[&;:<>{}\/]/",
                        'match' => false,
                        'message' => $this->translator->trans('Characters not allowed'),
                    ]),
                ],
            ])
            ->add('content', TextareaType::class,[
                'required' => true,
                'attr' => [
                    'class' => 'textarea is-size-5-mobile',
                    'placeholder' => $this->translator->trans( 'Your message...')
                ],
                'label' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => "/[&;:<>{}\/]/",
                        'match' => false,
                        'message' => $this->translator->trans('Characters not allowed'),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
