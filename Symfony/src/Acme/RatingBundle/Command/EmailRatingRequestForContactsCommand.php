<?php

namespace Acme\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmailRatingRequestForContactsCommand extends ContainerAwareCommand
{
    private $contactsQueryText =
        "SELECT
            id AS contactId,
            email_address AS emailAddress,
            first_name AS firstName,
            last_name AS lastName,
            contact_happened_at AS contactHappenedAt
        FROM 
            contact
        WHERE
            sent_email_at IS NULL
        ORDER BY
            contact_happened_at ASC";

    private $contactStatement = null;
    private $contactsDataToSendEmailsTo = null;

    protected function configure()
    {
        $this->setName('email:rating_request_for_contacts');
        $this->setDescription('Sending rating requests for contacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadContactsDataToSendEmailsTo();
        $this->addEmailsToQueue();
        $this->sendAllEmailsInQueue();
    }

    private function addEmailsToQueue() {
        foreach($this->contactsDataToSendEmailsTo AS $contactData) {
            $this->addEmailToQueue($contactData);
            $this->setSentEmailFlagForContact($contactData);
        }
    }

    private function addEmailToQueue($contactData) {
        $message = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject('Értékelje ügyintézőnk munkáját');
        $message->setFrom(array('vidanet@rate.me.uk' => 'Vidanet'));
        $message->setTo($contactData['emailAddress']);
        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:Contact:requestRatingEmail.html.twig', 
            array('contactData' => $contactData)
        ));
        $this->getContainer()->get('mailer')->send($message);
    }

    private function setSentEmailFlagForContact($contactData) {
        $connection = $this->getContainer()->get('database_connection');

        $connection->update(
            'contact', 
            array(
                'sent_email_at' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => $contactData['contactId']
            )
        );
    }

    private function loadContactsDataToSendEmailsTo() {
        $connection = $this->getContainer()->get('database_connection');
        $this->contactStatement = $connection->executeQuery($this->contactsQueryText);
        $this->contactStatement->execute();
        $this->contactsDataToSendEmailsTo = $this->contactStatement->fetchAll();
    }

    private function sendAllEmailsInQueue() {
        $spool = $this->getContainer()->get('mailer')->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }
}

?>
