<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CalendarController
 * @package App\Controller
 * @Security("is_granted('ROLE_ADMIN')", statusCode=403, message="Access denied !")
 */
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
        if (!$event->getDescription()) {
            $description = 'N/C';
        }else{
            $description = $event->getDescription();
        }
        $description = \Soundasleep\Html2Text::convert($description);

        $calendar = Calendar::create('www.marcel-boungou.com')
            ->event(Event::create($event->getTitle())
                ->startsAt($event->getDatetime())
                ->endsAt($event->getDatetime())
                ->address($adresse)
                ->description($description)
            )
            ->get();
        if (!file_exists($projectRoot.'/public/documents/events_files/')) {
            mkdir($projectRoot.'/public/documents/events_files/', 0777, true);
        }
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
