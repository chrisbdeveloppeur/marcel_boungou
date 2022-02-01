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
        $io = new SymfonyStyle($input, $output);
        $event_id = $input->getArgument('event_id');

        if ($event_id) {
            $io->note(sprintf('Event id selected is: %s', $event_id));
            $this->sendReminder($event_id);
            $io->success('MailReminder sended for the event id : '. $event_id);
        }

        //if ($input->getOption('option1')) {
        //    // ...
        //}

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }


    public function sendReminder($event_id)
    {
        $event = $this->eventRepository->find($event_id);
        $email = (new Email())
            ->from('admin@mercalboungou.com')
            ->to('kenshin91cb@gmail.com','christian.boungou@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $date = new \DateTime();
        $date = $date->format('d/m/Y');
        //$dateEvent = new \DateTime($event->getDatetime()->format('d/m/Y'));
        //$eventMonthRemindDate = $dateEvent->modify('-1 month');
        //$eventDayRemindDate = $dateEvent->modify('-1 day');
        if (1 == 1){
            //dd('test');
            $this->mailer->send($email);
        }

    }

}
