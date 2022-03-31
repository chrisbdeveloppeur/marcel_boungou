<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/picture", name="picture_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class PictureController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add a Picture'),
            'redirect' => [
                'link' => $this->redirectToRoute('picture_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Pictures list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Picture $picture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Picture'),
            'redirect' => [
                'link' => $this->redirectToRoute('picture_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Pictures list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST", "GET"})
     */
    public function delete(Request $request, $id, EntityManagerInterface $entityManager, PictureRepository $pictureRepository): Response
    {
        $picture = $pictureRepository->find($id);
        $title = $picture->getTitle();
        $entityManager->remove($picture);
        $entityManager->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly : ').'<b>'.$title.'</b>');
        return $this->redirectToRoute('picture_index');
    }

    /**
     * @Route("/image/delete/{id}", name="image_delete")
     */
    public function deleteImg($id, Picture $picture, EntityManagerInterface $em, Request $request): Response
    {
        $picture->setImage(null);
        $em->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly'));
        return $this->redirect($request->headers->get('referer'));
    }
}
