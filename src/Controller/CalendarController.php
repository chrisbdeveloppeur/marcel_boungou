<?php

namespace App\Controller;


use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController
{
    /**
     * @Route("/calendar", name="calendar")
     */
    public function index()
    {
        $calendar = Calendar::create('Laracon online')
            ->event(Event::create('Creating calender feeds')
                ->startsAt(new \DateTime('28 January 2022 15:00'))
                ->endsAt(new \DateTime('28 January 2022 16:00'))
            )
            ->get();

        dd($calendar);
    }
}
