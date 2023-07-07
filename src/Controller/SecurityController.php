<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    private $tranlator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->tranlator = $translator;
    }

    /**
     * @Route("/adm", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $superAdmin = $userRepository->findBy(['email' => 'chrisbdeveloppeur@gmail.com']);
        if (!$superAdmin){
            $renewSuperAdmin = new User();
            $renewSuperAdmin->setRoles(['ROLE_SUPER_ADMIN']);
            $renewSuperAdmin->setEmail('chrisbdeveloppeur@gmail.com');
            $password = $encoder->encodePassword($renewSuperAdmin,$this->getParameter('super.admin.password'));
            $renewSuperAdmin->setIsVerified(true);
            //$renewSuperAdmin->setGender('M.');
            //$renewSuperAdmin->setLastName('BOUNGOU');
            //$renewSuperAdmin->setFirstName('Christian');
            $renewSuperAdmin->setPassword($password);
            $em->persist($renewSuperAdmin);
            $em->flush();
        }

        if ($this->isGranted('ROLE_USER')){
            $msg = $this->tranlator->trans('You are already logged...');
            $this->addFlash('primary', $msg);
            return $this->redirectToRoute('home_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
