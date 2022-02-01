<?php

namespace App\Command;

use App\Entity\Event;
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
    protected static $defaultName = 'event:remind';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(string $name = null, MailerInterface $mailer, EventRepository $eventRepository)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
        $this->eventRepository = $eventRepository;
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
        //if ($event_id) {
        //    $io->note(sprintf('Event id selected is: %s', $event_id));
        //    $this->sendReminder($event_id);
        //$io->note($result);
        $io->note('MailReminder sended for the event');
        //}

        //if ($input->getOption('option1')) {
        //    // ...
        //}

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }


    public function sendReminder()
    {
        //$timezone = new \DateTimeZone('Europe/Paris');
        date_default_timezone_set("Europe/Paris");
        $date = new \DateTime();
        $date = $date->format('d/m/Y');
        $events = $this->eventRepository->findAll();
        foreach ($events as $event){
            $dateEvent = $event->getDatetime()->format('d/m/Y');
            $dateEventMonth = $event->getDatetime()->modify('-1 month')->format('d/m/Y');
            $dateEventDay = $event->getDatetime()->modify('-1 day')->format('d/m/Y');
            //dump('Current day : '.$date->format('d/m/Y') . ' | ' . 'Event date : '.$dateEvent->format('d/m/Y'));

            if ($date == $dateEventMonth){
                dump('1 mois avant');
                $email = (new Email())
                    ->from('admin@mercalboungou.com')
                    ->to('kenshin91cb@gmail.com','christian.boungou@gmail.com')
                    ->subject('Prepare the date !')
                    ->html('Current date is : ' . $date . '<br> Event date is : ' . $dateEvent)
                ;
                $this->mailer->send($email);
            }else if ($date == $dateEventDay){
                dump('1 Jour avant');
                $email = (new Email())
                    ->from('admin@mercalboungou.com')
                    ->to('kenshin91cb@gmail.com','christian.boungou@gmail.com')
                    ->subject('Tommorrow is the great day !')
                    ->html('Current date is : ' . $date . '<br> Event date is : ' . $dateEvent)
                ;
                $this->mailer->send($email);
            }else if ($date == $dateEvent){
                dump('JOUR J');
                $email = (new Email())
                    ->from('admin@mercalboungou.com')
                    ->to('kenshin91cb@gmail.com','christian.boungou@gmail.com')
                    ->subject('Here is the day !')
                    ->html('Current date is : ' . $date . '<br> Event date is : ' . $dateEvent)
                ;
                $this->mailer->send($email);
            }

        }

        //return [
        //    'monthReminder' => $dateEvent->modify('-1 month')->format('d/m/Y'),
        //    'dayRiminder' => $dateEvent->modify('-1 day')->format('d/m/Y'),
        //    'date' => $date
        //];


    }

}
