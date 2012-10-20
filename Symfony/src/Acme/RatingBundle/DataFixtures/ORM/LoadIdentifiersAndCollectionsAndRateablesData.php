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
        $collection = $this->createCollectionWithIdentifier($manager, 
            'Kék Osztriga Bár', 
            $this->createIdentifier($manager, 'http://www.one.com', '1111')
        );

        $this->createRateableWithCollection($manager,
            'Érték Elek',
            'Felszolgáló',
            $collection
        );

        $this->createRateableWithCollection($manager,
            'Jó Áron',
            'Üzletvezető',
            $collection
        );

        $this->createRateableWithCollection($manager,
            'Mézga Géza',
            'Konyhafőnök',
            $collection
        );

        $this->createRateableWithIdentifier($manager,
            'Kovács Béla',
            'Felszolgáló',
            $this->createIdentifier($manager, 'http://www.two.com', '2222')
        );
    }

    private function createIdentifier($manager, $qrCodeURL, $alphanumericValue)
    {
        $identifier = new Identifier();
        $identifier->setQrCodeUrl($qrCodeURL);
        $identifier->setAlphanumericValue($alphanumericValue);

        $manager->persist($identifier);
        $manager->flush();

        return $identifier;
    }

    private function createCollectionWithIdentifier($manager, $name, $identifier)
    {
        $collection = new RateableCollection();
        $collection->setName($name);
        $collection->setIdentifier($identifier);

        $manager->persist($collection);
        $manager->flush();

        return $collection;
    }

    private function createRateableWithIdentifier($manager, $name, $typeName, $identifier)
    {
        $rateable = new Rateable();
        $rateable->setName($name);
        $rateable->setTypeName($typeName);
        $rateable->setIdentifier($identifier);

        $manager->persist($rateable);
        $manager->flush();
    }

    private function createRateableWithCollection($manager, $name, $typeName, $collection)
    {
        $rateable = new Rateable();
        $rateable->setName($name);
        $rateable->setTypeName($typeName);
        $rateable->setCollection($collection);

        $manager->persist($rateable);
        $manager->flush();
    }
}
