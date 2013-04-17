<?php

namespace Acme\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmailRatingRequestForContactsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('email:rating_request_for_contacts');
        $this->setDescription('Sending rating requests for contacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject('Hello Email');
        $message->setFrom(array('vidanet@rate.me.uk' => 'Vidanet'));
        $message->setTo('ilsinszkitamas@gmail.com');
        $message->setBody('mizu?');

        $container = $this->getContainer();
        $mailer = $container->get('mailer');

        $mailer->send($message);

        $spool = $mailer->getTransport()->getSpool();
        $transport = $container->get('swiftmailer.transport.real');

        $spool->flushQueue($transport);
    }
}

?>
