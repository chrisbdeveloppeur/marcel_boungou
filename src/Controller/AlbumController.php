<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/album", name="album_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AlbumController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('album/index.html.twig', [
            'albums' => $albumRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'album' => $album,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add new Album'),
            'redirect' => [
                'link' => $this->redirectToRoute('album_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Albums list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Album $album): Response
    {
        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Album $album, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'album' => $album,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Album'),
            'redirect' => [
                'link' => $this->redirectToRoute('album_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Albums list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST", "GET"})
     */
    public function delete(Request $request, $id, EntityManagerInterface $entityManager, AlbumRepository $albumRepository): Response
    {
        $album = $albumRepository->find($id);
        $title = $album->getName();
        $entityManager->remove($album);
        $entityManager->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly : ').'<b>'.$title.'</b>');
        return $this->redirectToRoute('discography_index');
    }

    /**
     * @Route("/image/delete/{id}", name="image_delete")
     */
    public function deleteImg($id, Album $album, EntityManagerInterface $em, Request $request): Response
    {
        $album->setImage(null);
        $em->flush();
        $this->addFlash('info', $this->translator->trans('Image removed successfully'));
        return $this->redirect($request->headers->get('referer'));
    }

}
