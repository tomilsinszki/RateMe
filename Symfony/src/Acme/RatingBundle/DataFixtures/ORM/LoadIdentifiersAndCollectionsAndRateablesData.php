<?php

namespace Acme\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\RatingBundle\Entity\Identifier;
use Acme\RatingBundle\Entity\RateableCollection;
use Acme\RatingBundle\Entity\Rateable;

class LoadIdentifierData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $collectionIdentifier = new Identifier();
        $collectionIdentifier->setQrCodeUrl('http://www.one.com');
        $collectionIdentifier->setAlphanumericValue('1111');

        $collection = new RateableCollection();
        $collection->setName('my collection');
        $collection->setIdentifier($collectionIdentifier);

        $rateableIdentifier = new Identifier();
        $rateableIdentifier->setQrCodeUrl('http://www.two.com');
        $rateableIdentifier->setAlphanumericValue('2222');

        $rateable = new Rateable();
        $rateable->setName('my rateable');
        $rateable->setTypeName('my type');
        $rateable->setimageURL('http://www.image.com');
        $rateable->setIdentifier($rateableIdentifier);

        $manager->persist($collectionIdentifier);
        $manager->persist($collection);
        $manager->persist($rateableIdentifier);
        $manager->persist($rateable);
        $manager->flush();
    }
}
