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
        /*
        $jpgs = scandir('../assets/images/jpg/');

        foreach ($jpgs as $jpg){
                $file = file('../assets/images/jpg/'.$jpg);
                $old_name = $jpg;
                $new_name = str_replace(' ','',$old_name);
                if (copy($file,'../assets/images/jpg/'.$new_name)) {
                    unlink('../assets/images/jpg/'.$jpg);
                }
        }
        */

        return $this->render('home/index.html.twig', [

        ]);
    }
}
