<?php

namespace App\Controller;

use App\Command\NewsSenderCommand;
use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/news", name="news_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class NewsController extends AbstractController
{
    private $translator;
    private $newsSenderCommande;

    public function __construct(TranslatorInterface $translator, NewsSenderCommand $newsSenderCommande)
    {
        $this->translator = $translator;
        $this->newsSenderCommande = $newsSenderCommande;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($news);
            $entityManager->flush();
            $msg = $this->translator->trans('News ').'<b>'.$news->getTitle().'</b>'.$this->translator->trans(' created with success');
            $this->addFlash('success',$msg);
            return $this->redirectToRoute('news_index', [], Response::HTTP_SEE_OTHER);
        }

        $redirect_link = $this->redirectToRoute('news_index')->getTargetUrl();
        return $this->render('themes/just_the_form.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Add a News'),
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Back to News list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $msg = $this->translator->trans('Changes made successfully');
            $this->addFlash('success',$msg);
            return $this->redirectToRoute('news_index', [], Response::HTTP_SEE_OTHER);
        }

        $redirect_link = $this->redirectToRoute('news_index')->getTargetUrl();
        return $this->render('themes/just_the_form.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit News'),
            'redirect' => [
                'link' => $redirect_link,
                'txt' => $this->translator->trans('Back to News list'),
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager->remove($news);
            $entityManager->flush();
        }
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly'));
        return $this->redirectToRoute('news_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/send-news/{id}", name="news_sender")
     */
    public function newsSender($id, NewsRepository $newsRepository, SubscriberRepository $subscriberRepository): Response
    {
        $subscribers = $subscriberRepository->findAll();
        $nbSubscribers = count($subscribers);
        $news = $newsRepository->find($id);
        $this->newsSenderCommande->sendNews($news);
        $this->addFlash('warning', $this->translator->trans('The newsletter ').'<b>'.$news->getTitle().'</b>'.$this->translator->trans(' has been sent to ').'<b>'.$nbSubscribers.'</b>'.$this->translator->trans(' subscribers'));
        return $this->redirectToRoute('news_index', [], Response::HTTP_SEE_OTHER);
    }
}
