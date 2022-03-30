<?php

namespace App\Command;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Repository\SubscriberRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class NewsSenderCommand extends Command
{
    private $mailer;
    private $newsRepository;
    private $subscriberRepository;
    protected static $defaultName = 'news:sender';
    protected static $defaultDescription = 'Add a short description for your command';
    private $projectRoot;

    public function __construct(string $name = null, MailerInterface $mailer, NewsRepository $newsRepository, SubscriberRepository $subscriberRepository, string $projectRoot)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
        $this->newsRepository = $newsRepository;
        $this->subscriberRepository = $subscriberRepository;
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
        $news = $this->newsRepository->findOneBy(
            [],
            ['datetime' => 'DESC']
        );
        $this->sendNews($news);
        $io->note('MailReminder sended for the event');

        $io->success('Mail reminder sended !');

        return 0;
    }


    public function sendNews(News $news)
    {
        //date_default_timezone_set("Europe/Paris");
        //$date = new \DateTime();
        //$date = $date->format('d/m/Y');
        $subscribers = $this->subscriberRepository->findAll();
        if ($news->getTitle()){
            $title = $news->getTitle();
        }else{
            $title = 'Voici les nouvelles';
        }
        $email = (new TemplatedEmail())
            ->from('admin@marcelboungou.com')
            ->subject($title)
            ->htmlTemplate('emails/news.html.twig')
            ->context([
                'news' => $news,
            ])
        ;
        foreach ($subscribers as $subscriber){
            $email->addTo($subscriber->getEmail());
        }
        $this->mailer->send($email);

    }
}
