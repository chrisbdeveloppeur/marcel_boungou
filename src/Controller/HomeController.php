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
    public function index(CalendarController $calendarController): Response
    {
        $calendarController->calendar();
        $highlights_dates = [strval(date("d/m/Y", time() - 60 * 60 * 24)),strval(date("d/m/Y", time() + 60 * 60 * 24))];
        return $this->render('includes/calendar.html.twig', [
            'highlights_dates' => $highlights_dates,
        ]);
    }
}
