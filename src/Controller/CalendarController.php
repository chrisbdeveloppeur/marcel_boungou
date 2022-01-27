<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\HttpKernel\KernelInterface;

class CalendarController
{
    private $eventRepository;
    private $appKernel;

    public function __construct(EventRepository $eventRepository, KernelInterface $appKernel)
    {
        $this->eventRepository = $eventRepository;
        $this->appKernel = $appKernel;
    }

    public function createIcsFile($event_id)
    {
        $projectRoot = $this->appKernel->getProjectDir();
        $event = $this->eventRepository->find($event_id);
        $adresse = $event->getStreet().', '.$event->getCp().' '.$event->getCity().', '.$event->getCity();
        $calendar = Calendar::create('www.marcel-boungou.com')
            ->event(Event::create($event->getTitle())
                ->startsAt($event->getDatetime())
                ->endsAt($event->getDatetime())
                ->address($adresse)
                ->description($event->getDescription())
            )
            ->get();
        file_put_contents($projectRoot.'/public/documents/events_files/'.$event->getTitle().'.ics',$calendar);

        return $calendar;
    }

    public function deleteIcsFile($event_id)
    {
        $event = $this->eventRepository->find($event_id);
        unlink('../public/documents/events_files/'.$event->getTitle().'.ics');
        //file_put_contents('../public/documents/events_files/'.$event_title.'.ics');
    }
}
