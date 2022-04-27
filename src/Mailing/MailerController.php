<?php

namespace App\Controller\Mailer;

use App\Entity\Message;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    private $mailerInterface;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailerInterface = $mailerInterface;
    }

    public function sendMessageContact(Message $message)
    {
        $email = (new TemplatedEmail())
            ->from('admin@marcel-boungou.com')
            ->to('admin@marcel-boungou.com')
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