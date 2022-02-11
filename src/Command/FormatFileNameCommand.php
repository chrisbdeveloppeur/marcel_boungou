<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FormatFileNameCommand extends Command
{
    protected static $defaultName = 'rename:files';
    protected static $defaultDescription = 'Add a short description for your command';
    private $projectRoot;

    public function __construct(string $name = null, string $projectRoot)
    {
        parent::__construct($name);
        $this->projectRoot = $projectRoot.'/';
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('directory', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dir = $this->projectRoot.$input->getArgument('directory').'/';

        if ($dir) {
            $io->note(sprintf('You defined directory target as: %s', $dir));
            $io->ask('That is correct?',null,null);
            //        POUR RENAME LES FICHIER IMG
            $files = array_slice(scandir($dir), 2);

            foreach ($files as $file){
                $old_name = $file;
                $new_name = strtolower(preg_replace('~[\\\\/:*?"<>|()&, \']~','',$old_name));
                rename($dir.$old_name,$dir.$new_name);
                $io->text($old_name.' formatted to : '.$new_name);
            }

            $io->success('Files from directory : '.$dir.' are now formatted !');
        }else{
            $io->error('Please enter a directory target (ex: rename:files my/directory/');
        }




        return 0;
    }
}
