<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tag", name="tag_")
 */
class TagsController extends AbstractController
{
    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     */
    public function addTag(): Response
    {
        dd($_POST['tag']);

    }
}
