<?php

namespace App\EventSubscriber;

use App\Repository\AlbumRepository;
use App\Repository\BookRepository;
use App\Repository\EventRepository;
use App\Repository\MusicRepository;
use App\Repository\NewsRepository;
use App\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig, $eventRepository, $musicRepository, $albumRepository, $bookRepository, $pictureRepository, $newsRepository;


    public function __construct(Environment $twig, EventRepository $eventRepository, MusicRepository $musicRepository, AlbumRepository $albumRepository, BookRepository $bookRepository, PictureRepository $pictureRepository, NewsRepository $newsRepository)
    {
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->musicRepository = $musicRepository;
        $this->albumRepository = $albumRepository;
        $this->bookRepository = $bookRepository;
        $this->pictureRepository = $pictureRepository;
        $this->newsRepository = $newsRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
//        $date = new \DateTime('now');
//        $events = $this->eventRepository->findByDate($date);
        $events = $this->eventRepository->findAll();
        $nextEvent = $this->eventRepository->findNextEvent();
        $allNextEvents = $this->eventRepository->findNextEvents();
        $allPastedEvents = $this->eventRepository->findPastedEvents();
        $musics = $this->musicRepository->findAll();
        $albums = $this->albumRepository->findByYear();
        $books = $this->bookRepository->findAll();
        $pictures = $this->pictureRepository->findAll();
        $news = $this->newsRepository->findAll();
        $this->twig->addGlobal('events', $events);
        $this->twig->addGlobal('nextEvent', $nextEvent);
        $this->twig->addGlobal('allNextEvents', $allNextEvents);
        $this->twig->addGlobal('$allPastedEvents', $allPastedEvents);
        $this->twig->addGlobal('musics', $musics);
        $this->twig->addGlobal('albums', $albums);
        $this->twig->addGlobal('books', $books);
        $this->twig->addGlobal('pictures', $pictures);
        $this->twig->addGlobal('news', $news);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
