<?php

namespace App\EventSubscriber;

use App\Repository\EventRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $eventRepository;

    public function __construct(Environment $twig, EventRepository $eventRepository)
    {
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        // ...
        $events = $this->eventRepository->findByDate();
        $this->twig->addGlobal('events', $events);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
