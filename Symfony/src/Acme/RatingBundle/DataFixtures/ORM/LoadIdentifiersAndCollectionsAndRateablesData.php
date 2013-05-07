<?php

namespace Acme\RatingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\RatingBundle\Entity\Identifier;
use Acme\RatingBundle\Entity\RateableCollection;
use Acme\RatingBundle\Entity\Rateable;
use Doctrine\ORM\EntityRepository;

class LoadIdentifierData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $company = $manager->getRepository("Acme\RatingBundle\Entity\Company")->findOneBy(array('name' => 'Vidanet'));
        $owner = $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'manager@manager.com'));

        $collection = $this->createCollectionWithIdentifier($manager, 
            'Somtel', 
            $this->createIdentifier($manager, 'http://www.somtel.hu', '1111'),
            $owner,
            $company
        );

        $this->createRateableWithCollection($manager,
            'Barkó Renáta',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'barko.renata')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Gőző Viktória',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'gozo.viktoria')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Járfás Nóra',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'jarfas.nora')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Jordanics Julianna',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'jordanics.julianna')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Kocsis Zsuzsanna',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'kocsis.zsuzsanna')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Kovács Márta',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'kovacs.marta')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Kutasi Krisztina',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'kutasi.krisztina')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Lapat Krisztina',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'lapat.krisztina')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Megla Dóra',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'megla.dora')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Mócsán Dea',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'mocsan.dea')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Molnár Edina',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'molnar.edina')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Molnár Tímea',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'molnar.timea')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Nagyné Balogh Tünde',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'nagyne.balogh.tunde')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Némethné Kámán Mária',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'nemethne.kaman.maria')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Révészné Borsos Anita',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'reveszne.borsos.anita')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Szűcs Ágnes',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'szucs.agnes')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Válint Sarolt',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'valint.sarolt')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Siposné Deák Tímea',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'siposne.deak.timea')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
        );

        $this->createRateableWithCollection($manager,
            'Kotfás Judit',
            $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'kotfas.judit')),
            'Telefonos ügyfélszolgálatos',
            $collection,
            TRUE
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

    private function createCollectionWithIdentifier($manager, $name, $identifier, $ownerUser, $company)
    {
        $collection = new RateableCollection();
        $collection->setName($name);
        $collection->setIdentifier($identifier);
        $collection->addOwner($ownerUser);
        $collection->setCompany($company);

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

    private function createRateableWithCollection($manager, $name, $rateableUser, $typeName, $collection, $isReachableViaTelephone)
    {
        $rateable = new Rateable();
        $rateable->setName($name);
        $rateable->setRateableUser($rateableUser);
        $rateable->setTypeName($typeName);
        $rateable->setCollection($collection);
        $rateable->setIsReachableViaTelephone($isReachableViaTelephone);

        $manager->persist($rateable);
        $manager->flush();
    }
}
