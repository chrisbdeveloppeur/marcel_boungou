<?php

namespace App\Controller;

use App\Form\SubscriberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function home(): Response
    {
        if ($_POST){
            dd($_POST);
        }
        return $this->render('home/index.html.twig');
    }
}
