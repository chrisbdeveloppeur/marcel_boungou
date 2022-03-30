<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index")
     */
    public function home(Request $request, SubscriberRepository $subscriberRepository, EntityManagerInterface $em): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if ($form->isValid()){
                $em->persist($subscriber);
                $em->flush();
                $this->addFlash('info', $this->translator->trans('Thank you for subscribing to the newsletter'));
                $previousUrl = $request->headers->get('referer');
                return $this->redirect($previousUrl);
            }else{
                $this->addFlash('danger', $this->translator->trans('Operation failed'));
                $url = $this->redirectToRoute('home_index')->getTargetUrl().'#subscribeForm';
                return $this->redirect($url);
            }
        }
        return $this->render('home/index.html.twig',[
            'subs_form' => $form->createView(),
        ]);
    }
}
