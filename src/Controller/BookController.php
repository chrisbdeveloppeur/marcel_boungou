<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/book", name="book_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class BookController extends AbstractController
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add a new Book'),
            'redirect' =>[
                'link' => $this->redirectToRoute('bibliography_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Back to Bibliography page'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Book'),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly'));
        return $this->redirectToRoute('bibliography_index');
    }


    /**
     * @Route("/image/delete/{id}/{face?}", name="image_delete")
     */
    public function deleteImg($id, $face, Book $book, EntityManagerInterface $em, Request $request): Response
    {
        if($face == 'verso'){
            $book->setImageVersoFile(null);
        }else{
            $book->setImage(null);
        }
        $em->flush();
        $this->addFlash('info', $this->translator->trans('Image removed successfully'));
        return $this->redirect($request->headers->get('referer'));
    }

}
