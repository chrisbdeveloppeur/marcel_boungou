<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    /*
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')", statusCode=403, message="Access denied !")
     */
    /*
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    */

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @Security("user.getId() == id || is_granted('ROROLE_SUPER_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @Security("user.getId() == id || is_granted('ROLE_SUPER_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, $id): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $msg = $this->translator->trans('Saved account information');
            $this->addFlash('success', $msg);
            return $this->redirectToRoute('user_edit', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $redirectLink = $this->redirectToRoute('user_index')->getTargetUrl();
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'redirect' => [
                'txt' => $this->translator->trans('back to list'),
                'link' => $redirectLink,
            ],
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST","GET"})
     * @Security("user.getId() == id || is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
     */
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles()) )
        {
            $this->addFlash('danger', $this->translator->trans('The super manager account can\'t be deleted'));
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }else{
            $this->container->get('security.token_storage')->setToken(null);
            $em->remove($user);
            $em->flush();
            $this->addFlash('warning', $this->translator->trans('Account deleted !'));
            return $this->redirectToRoute('home_index');
        }
    }
}
