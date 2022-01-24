<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ToolsController extends AbstractController
{
    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        // On stock la lanque demandée dans la session
        $request->getSession()->set('_locale', $locale);
        $request->setLocale($locale);

        // On reviens sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
