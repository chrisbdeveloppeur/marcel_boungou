<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\MusicType;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/music", name="music_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class MusicController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(MusicRepository $musicRepository): Response
    {
        return $this->render('music/index.html.twig', [
            'music' => $musicRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $music = new Music();
        $form = $this->createForm(MusicType::class, $music);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($music);
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('The music').'<b>'.$music.'</b>'. $this->translator->trans('has been added') );
            return $this->redirectToRoute('discography_index');
        }

        return $this->render('themes/just_the_form.html.twig', [
            'music' => $music,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add a Music'),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Music $music): Response
    {
        return $this->render('music/show.html.twig', [
            'music' => $music,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Music $music, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MusicType::class, $music);
        $form->handleRequest($request);
        //dump($music);
        //dd($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($music);
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'music' => $music,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Music'),
            'redirect' => [
                'link' => $this->redirectToRoute('discography_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to discography'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Music $music, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$music->getId(), $request->request->get('_token'))) {
            $entityManager->remove($music);
            $entityManager->flush();
        }
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly'));
        return $this->redirectToRoute('discography_index');
    }

}
