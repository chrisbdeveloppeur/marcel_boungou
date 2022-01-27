<?php

namespace App\Controller;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class CalendarController
{
    public function calendar($event_title)
    {
        $calendar = Calendar::create('Laracon online')
            ->event(Event::create('Creating calender feeds')
                ->startsAt(new \DateTime('28 January 2022 15:00'))
                ->endsAt(new \DateTime('28 January 2022 16:00'))
            )
            ->get();

        file_put_contents('../public/documents/events_files/'.$event_title.'.ics',$calendar);

        return $calendar;
    }
}
