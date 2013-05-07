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
        $this->generateCompanies($manager->getConnection());
        //$this->generateRandomContacts(10, $manager->getConnection());
    }

    private function generateCompanies($connection) {
            $connection->insert('company', array(
                'name' => 'Vidanet',
            ));
    }

    private function generateRandomContacts($contactCount, $connection) {
        for ($i=0; $i<$contactCount; $i++) {
            $firstName = $this->generateRandomString(mt_rand(5, 15));
            $lastName = $this->generateRandomString(mt_rand(5, 15));
            $emailAddress = $this->generateRandomEmailAddress();
            $contactedAt = $this->generateRandomDatetime();

            $connection->insert('verified_client', array(
                'client_id' => $this->generateRandomString(mt_rand(5, 15)),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email_address' => $emailAddress,
            ));

            $clientId = $connection->lastInsertId();
            
            $connection->insert('contact', array(
                'client_id' => $clientId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email_address' => $emailAddress,
                'contact_happened_at' => $contactedAt,
            ));
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
