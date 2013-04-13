<?php

namespace Acme\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\RatingBundle\Entity\Client;
use Acme\RatingBundle\Entity\Contact;
use Doctrine\ORM\EntityRepository;

class LoadTestContactAndClientData implements FixtureInterface
{
    public function load(ObjectManager $manager) {
        //$this->generateRandomContacts(10000, $manager->getConnection());
    }

    private function generateRandomContacts($contactCount, $connection) {
        for ($i=0; $i<$contactCount; $i++) {
            $connection->insert('client', array(
                'client_id' => $this->generateRandomString(mt_rand(5, 15)),
                'first_name' => $this->generateRandomString(mt_rand(5, 15)),
                'last_name' => $this->generateRandomString(mt_rand(5, 15)),
            ));

            $clientId = $connection->lastInsertId();

            $emailAddress = $this->generateRandomEmailAddress();
            $contactedAt = $this->generateRandomDatetime();
            
            $connection->insert('contact', array(
                'client_id' => $clientId,
                'email_address' => $emailAddress,
                'contacted_at' => $contactedAt,
            ));

            $contactId = $connection->lastInsertId();
        }
    }

    private function generateRandomDatetime() {
        return date("Y-m-d H:i:s", mt_rand(1262055681, 1363899714));
    }

    private function generateRandomEmailAddress() {
        $lengthOfEmailPrefix = mt_rand(5, 15);
        $emailPrefix = $this->generateRandomString($lengthOfEmailPrefix);
        
        $emailPostfixes = array('gmail.com', 'yahoo.com', 'msn.com', 'facebook.com');
        $emailPostfix = $emailPostfixes[array_rand($emailPostfixes)];

        return $emailPrefix.'@'.$emailPostfix;
    }

    private function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i=0; $i<$length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
