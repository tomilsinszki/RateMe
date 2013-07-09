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
        
        $somtelOwner = $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'somtel.manager'));
        $this->createSomtelCollectionAndRateables($manager, $somtelOwner, $company);

        $ksPartnerOwner = $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => 'kspartner.manager'));
        $this->createKsPartnerCollectionAndRateables($manager, $ksPartnerOwner, $company);
    }

    private function createSomtelCollectionAndRateables($manager, $owner, $company)
    {
        $somtelCollection = $this->createCollectionWithIdentifier($manager, 
            'Somtel', 
            $this->createIdentifier($manager, 'http://www.somtel.hu', '1111'),
            $owner,
            $company
        );

        $isReachableViaTelephone = TRUE;

        $rateables = array(
            'barko.renata' => 'Barkó Renáta',
            'gozo.viktoria' => 'Gőző Viktória',
            'jarfas.nora' => 'Járfás Nóra',
            'jordanics.julianna' => 'Jordanics Julianna',
            'kocsis.zsuzsanna' => 'Kocsis Zsuzsanna',
            'kovacs.marta' => 'Kovács Márta',
            'kutasi.krisztina' => 'Kutasi Krisztina',
            'lapat.krisztina' => 'Lapat Krisztina',
            'megla.dora' => 'Megla Dóra',
            'mocsan.dea' => 'Mócsán Dea',
            'molnar.edina' => 'Molnár Edina',
            'molnar.timea' => 'Molnár Tímea',
            'nagyne.balogh.tunde' => 'Nagyné Balogh Tünde',
            'nemethne.kaman.maria' => 'Némethné Kámán Mária',
            'reveszne.borsos.anita' => 'Révészné Borsos Anita',
            'szucs.agnes' => 'Szűcs Ágnes',
            'valint.sarolt' => 'Válint Sarolt',
            'siposne.deak.timea' => 'Siposné Deák Tímea',
            'kotfas.judit' => 'Kotfás Judit',
        );

        foreach( $rateables AS $userName => $rateableName ) {
            $this->createRateableWithCollection($manager,
                $rateableName,
                $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => $userName)),
                'Telefonos ügyfélszolgálatos',
                $somtelCollection,
                $isReachableViaTelephone
            );
        }
    }

    private function createKsPartnerCollectionAndRateables($manager, $owner, $company)
    {
        $ksPartnerCollection = $this->createCollectionWithIdentifier($manager, 
            'KS Partner', 
            $this->createIdentifier($manager, 'http://www.kspartner.hu', '2222'),
            $owner,
            $company
        );
        
        $isReachableViaTelephone = FALSE;
        
        $rateables = array(
            'acs-gergely.zita' => 'Ács-Gergely Zita',
            'adamecz.eva' => 'Adamecz Éva',
            'bekesi.alexandra' => 'Békesi Alexandra',
            'fisli.tiborne' => 'Fisli Tiborné',
            'juhasz.katalin' => 'Juhász Katalin',
            'kovacs.gyula' => 'Kovács Gyula',
            'menyhart.janos' => 'Menyhárt János',
            'szatmari.dora' => 'Szatmári Dóra',
            'szilvas.aniko' => 'Szilvás Anikó',
            'vrabel.mihaly' => 'Vrábel Mihály',
            'bertha.viktoria' => 'Bertha Viktória',
            'major.edith' => 'Major Edith',
            'arbogaszt.fanni' => 'Árbogászt Fanni',
        );
        
        foreach( $rateables AS $userName => $rateableName ) {
            $this->createRateableWithCollection($manager,
                $rateableName,
                $manager->getRepository("Acme\UserBundle\Entity\User")->findOneBy(array('username' => $userName)),
                'Ügyfélszolgálatos',
                $ksPartnerCollection,
                $isReachableViaTelephone
            );
        }
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
