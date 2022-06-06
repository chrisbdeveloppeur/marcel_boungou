<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\Subscriber1Type;
use App\Form\SubscriberType;
use App\Repository\EventRepository;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/subscrib", name="subscrib_")
 */
class SubscribController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

//    /**
//     * @Route("/news/add", name="news_add", methods={"post"})
//     */
//    public function newsAdd(Request $request): Response
//    {
//        return $this->render('home/index.html.twig', [
//
//        ]);
//    }


//    /**
//     * @Route("/news/remove, name="news_remove")
//     */
    /*
    public function newsRemove(): Response
    {
        return $this->render('subscrib/index.html.twig', [
            'controller_name' => 'SubscribController',
        ]);
    }
    */

    /**
     * @Route("/remove/all", name="remove_all")
     */
    public function removeAll(EventRepository $eventRepository, SubscriberRepository $subscriberRepository, Request $request, EntityManagerInterface $em): Response
    {
        $events = $eventRepository->findAll();
        $form = $this->createForm(SubscriberType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $email = $form->get('email')->getData();
            $subscriber = $subscriberRepository->findOneBy(['email' => $email]);
            if ($subscriber){
                $em->remove($subscriber);
            }
            foreach ($events as $event){
                $event->removeMailToRemind($email);
            }
            $em->flush();
            $this->addFlash('info', $this->translator->trans('Successfully unsubscribed for email : '). $email);
        }

        return $this->render('themes/just_the_form.html.twig',[
            'form' => $form->createView(),
            'form_info' => $this->translator->trans('You are about to unsubscribe from the site, you will no longer receive any emails from www.marcel-boungou.com')
        ]);
    }




    /**
     * @Route("/subscribers/index", name="subscriber_index", methods={"GET"})
     */
    public function index(SubscriberRepository $subscriberRepository): Response
    {
        return $this->render('subscriber/index.html.twig', [
            'subscribers' => $subscriberRepository->findAll(),
        ]);
    }

    /**
     * @Route("/subscribers/new", name="subscriber_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {

        // SUBSCRIBER FORM CONTROL
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if ($form->isValid()){
                $em->persist($subscriber);
                $em->flush();
                $this->addFlash('info', $this->translator->trans('Subscriber add successfully : ').'<b>'.$subscriber.'</b>');
                return $this->redirectToRoute('subscrib_subscriber_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $errMsg = $form->get('email')->getErrors()->current()->getMessage();
                $this->addFlash('danger', $this->translator->trans('Operation failed').'<br>'.$errMsg);
                return $this->redirectToRoute('subscrib_subscriber_new', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('themes/just_the_form.html.twig', [
            'subscriber' => $subscriber,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Create new Subscriber'),
            'redirect' => [
                'link' => $this->redirectToRoute('subscrib_subscriber_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Subscribers list'),
            ]
        ]);
    }



    /**
     * @Route("/subscribers/{id}/show", name="subscriber_show", methods={"GET"})
     */
    public function show(Subscriber $subscriber): Response
    {
        return $this->render('subscriber/show.html.twig', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * @Route("/subscribers/{id}/edit", name="subscriber_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subscriber $subscriber, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Subscriber1Type::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', $this->translator->trans('Changes made successfully'));
            return $this->redirectToRoute('subscrib_subscriber_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('themes/just_the_form.html.twig', [
            'subscriber' => $subscriber,
            'form' => $form->createView(),
            'form_title' => $this->translator->trans('Edit Subscriber'),
            'redirect' => [
                'link' => $this->redirectToRoute('subscrib_subscriber_index')->getTargetUrl(),
                'txt' => $this->translator->trans('Redirect to Subscribers list'),
            ]
        ]);
    }

    /**
     * @Route("/subscribers/{id}/delete", name="subscriber_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Subscriber $subscriber, EntityManagerInterface $entityManager, EntityManagerInterface $em): Response
    {
        $email = $subscriber->getEmail();
        $em->remove($subscriber);
        $em->flush();
        $this->addFlash('warning', $this->translator->trans('Element deleted successfuly : ').'<b>'.$email.'</b>');
        return $this->redirect($request->headers->get('referer'));
    }

}
