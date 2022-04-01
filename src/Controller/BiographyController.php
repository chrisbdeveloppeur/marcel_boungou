<?php

namespace App\Controller;

use App\Form\BiographyType;
use App\Repository\BiographyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/biography", name="biography_")
 */
class BiographyController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('biography/index.html.twig', [
            'controller_name' => 'BiographyController',
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(BiographyRepository $biographyRepository, Request $request, EntityManagerInterface $em): Response
    {
        $biography = $biographyRepository->findAll()[0];
        $form = $this->createForm(BiographyType::class, $biography);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'biography' => $biography,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Biography'),
            'redirect' => [
                'link' => $this->redirectToRoute('home_index')->getTargetUrl().'#bio',
                'txt' => $this->translator->trans('Redirect to Biography'),
            ]
        ]);
    }
}
