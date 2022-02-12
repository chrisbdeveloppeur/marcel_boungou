<?php

namespace App\Command;

use App\Repository\EventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EventReminderCommand extends Command
{
    private $mailer;
    private $eventRepository;
    protected static $defaultName = 'event:reminder';
    protected static $defaultDescription = 'Add a short description for your command';
    private $projectRoot;

    public function __construct(string $name = null, MailerInterface $mailer, EventRepository $eventRepository, string $projectRoot)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
        $this->eventRepository = $eventRepository;
        $this->projectRoot = $projectRoot;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('event_id', InputArgument::OPTIONAL, 'id of the event selected')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        date_default_timezone_set('Europe/Paris');
        $io = new SymfonyStyle($input, $output);
        $this->sendReminder();
        $io->note('MailReminder sended for the event');

        $io->success('Mail reminder sended !');

        return 0;
    }


    public function sendReminder()
    {
        date_default_timezone_set("Europe/Paris");
        $date = new \DateTime();
        $date = $date->format('d/m/Y');
        $events = $this->eventRepository->findAll();
        foreach ($events as $event){
            $adresse = $event->getStreet(). ', ' .$event->getCp(). ' ' .$event->getCity(). ', ' .$event->getCountry();
            $dateEvent = $event->getDatetime()->format('d/m/Y');

            $dateEventMonth = $event->getDatetime()->format('d-m-Y');
            $dateEventMonth = new \DateTime($dateEventMonth);
            $dateEventMonth = $dateEventMonth->modify('-1 month')->format('d/m/Y');

            $dateEventWeek = $event->getDatetime()->format('d-m-Y');
            $dateEventWeek = new \DateTime($dateEventWeek);
            $dateEventWeek = $dateEventWeek->modify('-1 week')->format('d/m/Y');

            $dateEventDay = $event->getDatetime()->format('d-m-Y');
            $dateEventDay = new \DateTime($dateEventDay);
            $dateEventDay = $dateEventDay->modify('-1 day')->format('d/m/Y');

            $mails = $event->getMailsToRemind();

            if ($event->getTicketingLink()){
                $link = '<span>Vous n\'avez pas encore pris vos entrées ?  
                            <a href="'.$event->getTicketingLink().'">
                                <span style="font-size: large;font-weight: bold">
                                    Cliquez ici !
                                </span>
                            </a>
                         </span><br>';
            }else{
                $link ='';
            }
            if ($date == $dateEventMonth || $date == $dateEventWeek || $date == $dateEventDay){
                $email = (new Email())
                    ->from('admin@marcelboungou.com')
                    ->subject($event->getTitle())
                    ->html('<h1>'.$event->getTitle().'</h1>'
                        .$link
                        .'<br>Date de l\'événement : le <span style="font-size: large;font-weight: bold">' . $event->getDatetime()->format('d/m/Y') .'</span><br>'
                        .'Heure : <span style="font-size: large;font-weight: bold">'.$event->getDatetime()->format('H:i').'</span>'
//                        .'<a target="_blank" download="'.$event->getIcsFile().'" type=".ics"  href="'.$dir.$event->getIcsFile().'">
//                            Ajouter au calendier
//                        </a>'
                        . '<br><br><b>Adresse</b> : <a href="https://www.google.com/maps/search/'.$adresse.'">'.$adresse.'</a>'
                    )
                ;
                foreach ($mails as $mail){
                    $email->addTo($mail);
                }
                $this->mailer->send($email);
            }

//            if ($date == $dateEventWeek){
//                $email = (new Email())
//                    ->from('admin@mercalboungou.com')
//                    ->subject($event->getTitle())
//                    ->html('<h1>'.$event->getTitle().'</h1>' . $date . '<br>Date de l\'événement : ' . $event->getDatetime()->format('d/m/Y H:i')
//                        . '<br><br><b>Adresse</b> : <a href="https://www.google.com/maps/search/'.$adresse.'">'.$adresse.'</a>'
//                    )
//                ;
//                foreach ($mails as $mail){
//                    $email->addTo($mail);
//                }
//                $this->mailer->send($email);
//            }
//
//            if ($date == $dateEventDay){
//                $email = (new Email())
//                    ->from('admin@mercalboungou.com')
//                    ->subject($event->getTitle())
//                                        ->html('<h1 class="is-uppercase">'.$event->getTitle().'</h1>' . $date . '<br>Event date is : ' . $event->getDatetime()->format('d/m/Y H:i')
//                        . '<br><br><b>Adresse</b> : <a href="https://www.google.com/maps/search/'.$adresse.'">'.$adresse.'</a>'
//                    )
//                ;
//                foreach ($mails as $mail){
//                    $email->addTo($mail);
//                }
//                $this->mailer->send($email);
//            }

//            if ($date == $dateEvent){
//                $email = (new Email())
//                    ->from('admin@mercalboungou.com')
//                    ->subject($event->getTitle() . ' Jour J')
//                                        ->html('<h1>'.$event->getTitle().'</h1>' . $date . '<br>Event date is : ' . $dateEvent
//                        . '<br><br><b>Adresse</b> : <a href="https://www.google.com/maps/search/'.$adresse.'">'.$adresse.'</a>'
//                    )
//                ;
//                foreach ($mails as $mail){
//                    $email->addTo($mail);
//                }
//                $this->mailer->send($email);
//            }

        }

    }

}
