<?php

namespace App\Controller;

use App\Entity\Biography;
use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Repository\BiographyRepository;
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
    public function home(Request $request, SubscriberRepository $subscriberRepository, EntityManagerInterface $em, BiographyRepository $biographyRepository): Response
    {

        // SUBSCRIBER FORM CONTROL
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
            $url = $this->redirectToRoute('home_index')->getTargetUrl().'#subscribeForm';
            return $this->redirect($url);
        }

        // BIOGRAPHY CHECK / CONTROL
        $bios = $biographyRepository->findAll();
        $bioNb = count($bios);
        if ($bioNb > 0){
            $biography = $bios[0];
        }else{
            $biography = new Biography();
            $em->persist($biography);
            $em->flush();
        }

        return $this->render('home/index.html.twig',[
            'subs_form' => $form->createView(),
            'biography' => $biography,
        ]);
    }
}
