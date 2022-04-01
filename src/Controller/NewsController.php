<?php

namespace App\Controller;

use App\Command\NewsSenderCommand;
use App\Entity\News;
use App\Entity\Subscriber;
use App\Form\NewsType;
use App\Form\SubscriberType;
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
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findByYear(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
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
            $this->addFlash('info',$msg);
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
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $msg = $this->translator->trans('Changes made successfully');
            $this->addFlash('info',$msg);
            $previousUrl = $request->headers->get('referer');
            return $this->redirect($previousUrl);
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
     * @Route("/{id}/delete", name="delete", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, $id, EntityManagerInterface $entityManager, NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->find($id);
        $title = $news->getTitle();
        $entityManager->remove($news);
        $entityManager->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly : ').'<b>'.$title.'</b>');
        return $this->redirectToRoute('news_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @param NewsRepository $newsRepository
     * @return Response
     * @Route("/all", name="all", methods={"GET", "POST"})
     */
    public function allNews(NewsRepository $newsRepository, Request $request, EntityManagerInterface $em): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if ($form->isValid()){
                $em->persist($subscriber);
                $em->flush();
                $this->addFlash('info', $this->translator->trans('Thank you for subscribing to the newsletter'));
            }else{
                $errMsg = $form->get('email')->getErrors()->current()->getMessage();
                $this->addFlash('danger', $this->translator->trans('Operation failed').'<br>'.$errMsg);
            }
            $url = $this->redirectToRoute('news_all')->getTargetUrl();
            return $this->redirect($url);
        }
        $allNews = $newsRepository->findAll();
        return $this->render('news/_all.html.twig',[
            'news' => $allNews,
            'subs_form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/send-news/{id}", name="send")
     * @Security("is_granted('ROLE_ADMIN')")
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
