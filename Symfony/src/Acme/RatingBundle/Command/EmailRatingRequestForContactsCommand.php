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
            c.id AS contactId,
            c.email_address AS emailAddress,
            c.first_name AS firstName,
            c.last_name AS lastName,
            c.contact_happened_at AS contactHappenedAt,
            r.name AS rateableName,
            r.is_reachable_via_telephone AS rateableIsReachableViaTelephone,
            i.id AS imageFileName,
            i.path AS imageFileExtension
        FROM contact c
        LEFT JOIN rateable r ON c.rateable_id=r.id
        LEFT JOIN image i ON r.image_id=i.id
        WHERE sent_email_at IS NULL
        ORDER BY contact_happened_at ASC";

    private $contactStatement = null;
    private $contactsDataToSendEmailsTo = null;

    protected function configure()
    {
        $this->setName('email:rating_request_for_contacts');
        $this->setDescription('Sending rating requests for contacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('router')->getContext()->setHost('test.rate.me.uk');

        $this->loadContactsDataToSendEmailsTo();
        $this->addEmailsToQueue();
        $this->sendAllEmailsInQueue();
    }

    private function addEmailsToQueue() {
        foreach($this->contactsDataToSendEmailsTo AS $contactData) {
            $rateToken = $this->generateRandomRateToken($contactData);
            $this->addEmailToQueue($contactData, $rateToken);
            $this->setSentEmailFlagForContact($contactData, $rateToken);
        }
    }

    private function addEmailToQueue($contactData, $rateToken) {
        $message = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject('Értékelje ügyintézőnk munkáját');
        $message->setFrom(array('vidanet@rate.me.uk' => 'Vidanet'));
        $message->setTo($contactData['emailAddress']);
        $embeddedImages = $this->embedImagesIntoMessage($contactData, $message);
        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:Contact:requestRatingEmail.html.twig', 
            array('contactData' => $contactData, 'images' => $embeddedImages, 'rateToken' => $rateToken)
        ));
        $this->getContainer()->get('mailer')->send($message);
    }

    private function embedImagesIntoMessage($contactData, $message) {
        $webRootPath = $this->getContainer()->get('kernel')->getRootDir()."/../web";
        
        $images = array(
            'profile' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/uploads/images/{$contactData['imageFileName']}.{$contactData['imageFileExtension']}")),
            'logo' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailLogo.png")),
            'star1' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailStar1.png")),
            'star2' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailStar2.png")),
            'star3' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailStar3.png")),
            'star4' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailStar4.png")),
            'star5' => $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailStar5.png")),
        );

        return $images;
    }

    private function setSentEmailFlagForContact($contactData, $rateToken) {
        $connection = $this->getContainer()->get('database_connection');

        $connection->update(
            'contact', 
            array(
                'sent_email_at' => date('Y-m-d H:i:s'),
                'rate_token' => $rateToken,
            ),
            array(
                'id' => $contactData['contactId']
            )
        );
    }

    private function generateRandomRateToken($contactData) {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $rateToken = '';

        for ($i=0; $i<10; $i++) {
            $rateToken .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        $rateToken .= $contactData['contactId'];

        return $rateToken;
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
