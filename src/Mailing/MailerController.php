<?php

namespace App\Mailing;

use App\Entity\Message;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController
{
    private $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendMessageContact(Message $message)
    {
        $email = (new TemplatedEmail())
            ->from('contact@marcel-boungou.com')
            ->to('contact@marcel-boungou.com')
            ->subject('Message de '.$message->getSender())
            ->htmlTemplate('emails/message.html.twig', [
                'message' => $message,
            ])
            ->context([
                'message' => $message,
            ])
        ;

        $this->mailerInterface->send($email);

    }
}