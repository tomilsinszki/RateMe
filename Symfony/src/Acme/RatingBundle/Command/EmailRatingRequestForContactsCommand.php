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
        $this
            ->setName('email:rating_request_for_contacts')
            ->setDescription('Sending rating requests for contacts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello');
    }
}

?>
