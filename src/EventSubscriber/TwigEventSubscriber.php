<?php

namespace App\EventSubscriber;

use App\Repository\EventRepository;
use App\Repository\MusicRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $eventRepository;
    private $musicRepository;

    public function __construct(Environment $twig, EventRepository $eventRepository, MusicRepository $musicRepository)
    {
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->musicRepository = $musicRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        // ...
        $events = $this->eventRepository->findByDate();
        $musics = $this->musicRepository->findAll();
        $this->twig->addGlobal('events', $events);
        $this->twig->addGlobal('musics', $musics);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
