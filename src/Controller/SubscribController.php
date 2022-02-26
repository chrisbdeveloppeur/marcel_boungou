<?php

namespace App\Controller;

use App\Form\SubcriberType;
use App\Form\SubscriberType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscrib", name="subscrib_")
 */
class SubscribController extends AbstractController
{
    /**
     * @Route("/news/add", name="news_add")
     */
    /*
    public function newsAdd(): Response
    {
        return $this->render('subscrib/index.html.twig', [
            'controller_name' => 'SubscribController',
        ]);
    }
*/

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
    public function removeAll(EventRepository $eventRepository, Request $request): Response
    {
        $form = $this->createForm(SubscriberType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

        }

        return $this->render('themes/just_the_form.html.twig',[
            'form' => $form->createView()
        ]);
    }


}
