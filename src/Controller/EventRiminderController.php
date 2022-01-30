<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EventRiminderController
{

    /**
     * @param Event $event
     * @throws \Exception
     */
    public function sendReminder(Event $event, MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('admin@mercalboungou.com')
            ->to(['kenshin91cb@gmail.com','christian.boungou@gmail.com'])
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $date = new \DateTime();
        $date = $date->format('d/m/Y');
        $dateEvent = new \DateTime($event->getDatetime()->format('d/m/Y'));
        $eventMonthRemindDate = $dateEvent->modify('-1 month');
        $eventDayRemindDate = $dateEvent->modify('-1 day');
        if (1 == 1){
            dd('test');
            $mailer->send($email);
        }

    }

}
