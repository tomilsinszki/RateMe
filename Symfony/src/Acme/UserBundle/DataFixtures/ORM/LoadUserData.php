<?php

namespace Acme\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Acme\UserBundle\Entity\User;
use Acme\UserBundle\Entity\Group;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $raterGroup = $this->createRaterGroup($manager, 'rater', 'ROLE_RATER');
        $managerGroup = $this->createRaterGroup($manager, 'manager', 'ROLE_MANAGER');
        $customerServiceGroup = $this->createRaterGroup($manager, 'customerservice', 'ROLE_CUSTOMERSERVICE');
        
        $this->createUserWithGroup($manager, 'somtel.manager', 'manager', $managerGroup);
        $this->createUserWithGroup($manager, 'kspartner.manager', 'manager', $managerGroup);

        $this->createUserWithGroup($manager, 'vidanet', 'admin123', $managerGroup);
        $this->createUserWithGroup($manager, 'kspartner', 'ksadmin123', $managerGroup);
        $this->createUserWithGroup($manager, 'somtel', 'somadmin123', $managerGroup);

        $this->createVidanetUsers($manager, $customerServiceGroup);
        $this->createSomtelUsers($manager, $customerServiceGroup);
        $this->createKsPartnerUsers($manager, $customerServiceGroup);
    }

    private function createVidanetUsers($manager, $customerServiceGroup) {
        $userNames = array(
            'baranyaine.herold.andrea',
            'varga.andrea',
            'bokor.zsuzsanna',
            'ihasz.bea',
            'wachtler.agota',
            'szoke.veronika',
            'varga.ibolya',
            'becsi.nikolett',
            'vegh.zsuzsanna',
            'fordos.andrea',
            'hende.judit',
            'freyne.anka.zsuzsanna',
            'nemedi.rita',
            'eizler.kitti',
            'szenasy.zoltanne',
            'nyiri.agnes',
            'petroviczne.szalai.veronika',
        );

        $this->createUsers($manager, $customerServiceGroup, $userNames, 'vidanet123');
    }

    private function createSomtelUsers($manager, $customerServiceGroup) {
        $userNames = array(
            'barko.renata',
            'gozo.viktoria',
            'jarfas.nora',
            'jordanics.julianna',
            'kocsis.zsuzsanna',
            'kovacs.marta',
            'kutasi.krisztina',
            'lapat.krisztina',
            'megla.dora',
            'mocsan.dea',
            'molnar.edina',
            'molnar.timea',
            'nagyne.balogh.tunde',
            'nemethne.kaman.maria',
            'reveszne.borsos.anita',
            'szucs.agnes',
            'valint.sarolt',
            'siposne.deak.timea',
            'kotfas.judit',
        );

        $this->createUsers($manager, $customerServiceGroup, $userNames, 'somtel123');
    }

    private function createKsPartnerUsers($manager, $customerServiceGroup) {
        $userNames = array(
            'acs-gergely.zita',
            'adamecz.eva',
            'bekesi.alexandra',
            'fisli.tiborne',
            'juhasz.katalin',
            'kovacs.gyula',
            'menyhart.janos',
            'szatmari.dora',
            'szilvas.aniko',
            'vrabel.mihaly',
            'bertha.viktoria',
            'major.edith',
            'arbogaszt.fanni',
        );
        
        $this->createUsers($manager, $customerServiceGroup, $userNames, 'kspartner123');
    }
    
    private function createUsers($manager, $group, $userNames, $defaultPassword) {
        foreach( $userNames AS $userName ) {
            $this->createUserWithGroup($manager, $userName, $defaultPassword, $group);
        }
    }
    
    private function createRaterGroup(ObjectManager $manager, $name, $roleName) {
        $group = new Group();
        $group->setName($name);
        $group->setRole($roleName);
        $manager->persist($group);

        return $group;
    }

    private function createUserWithGroup(ObjectManager $manager, $username, $password, $group) {
        $user = new User();

        $user->setUsername($username);
        $user->setSalt(md5(time()));

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($password);

        $user->addGroup($group);

        $manager->persist($user);
        $manager->flush();

        return $user;
    }
}
