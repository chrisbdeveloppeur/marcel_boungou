<?php

namespace App\Controller;

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

//        POUR RENAME LES FICHIER IMG
//        $jpgs = array_slice(scandir('../assets/images/jpg/'), 2);
//
//        foreach ($jpgs as $jpg){
//                $old_name = $jpg;
//                $new_name = strtolower(preg_replace('~[\\\\/:*?"<>|()&, \']~','',$old_name));
//                rename('../assets/images/jpg/'.$old_name,'../assets/images/jpg/'.$new_name);
//        }


        return $this->render('home/index.html.twig', [

        ]);
    }
}
