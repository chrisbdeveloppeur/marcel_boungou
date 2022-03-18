<?php

namespace App\Controller;

use App\Form\SubcriberType;
use App\Form\SubscriberType;
use App\Repository\EventRepository;
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

    /**
     * @Route("/news/add", name="news_add", methods={"post"})
     */
    public function newsAdd(Request $request): Response
    {
        dd($request);
        return $this->render('home/index.html.twig', [

        ]);
    }


    /**
     * @Route("/news/remove, name="news_remove")
     */
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
    public function removeAll(EventRepository $eventRepository, Request $request, EntityManagerInterface $em): Response
    {
        $events = $eventRepository->findAll();
        $form = $this->createForm(SubscriberType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $email = $form->get('email')->getData();
            foreach ($events as $event){
                $event->removeMailToRemind($email);
                $em->flush();
            }
            $this->addFlash('info', $this->translator->trans('Successfully unsubscribed for email : '). $email);
        }

        return $this->render('themes/just_the_form.html.twig',[
            'form' => $form->createView()
        ]);
    }


}
