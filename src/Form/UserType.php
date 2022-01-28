<?php

namespace App\Form;

use App\Entity\User;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserType extends AbstractType
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'attr' => [
                    'class' => 'input'
                ]
            ])
            /*
            ->add('password', PasswordType::class,[
                'attr' => [
                    'class' => 'input'
                ]
            ])

            ->add('imageFile', VichFileType::class,[
                'attr' => [
                    'class' => 'file-input'
                ]
            ])
            */
        ;
        if(in_array('ROLE_SUPER_ADMIN',$this->user->getRoles())){
            $builder
                ->add('isVerified', CheckboxType::class,[
                    'label' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
