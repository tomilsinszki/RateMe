<?php

namespace Acme\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\RatingBundle\Entity\Identifier;

class LoadIdentifierData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $identifier = new Identifier();
        $identifier->setQrCodeUrl('http://www.index.hu');
        $identifier->setAlphanumericValue('1234');

        $manager->persist($identifier);
        $manager->flush();
    }
}
