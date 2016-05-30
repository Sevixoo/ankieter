<?php
namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Controller\MailController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MyCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('flush')
            ->setDescription('Descripción de lo que hace el comando')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Explicamos el significado del argumento');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer = $this->getContainer()->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');

        $spool->setMessageLimit(100);
        $spool->flushQueue($transport);

        $output->writeln('Flush');
    }
}