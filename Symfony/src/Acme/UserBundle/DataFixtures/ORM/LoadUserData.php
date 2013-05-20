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
        
        $userNamesForSomtel = array(
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

        $this->createUsersForSomtel($manager, $customerServiceGroup, $userNamesForSomtel, 'somtel123');

        $userNamesForKsPartner = array(
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
        );
        
        $this->createUsersForKsPartner($manager, $customerServiceGroup, $userNamesForKsPartner, 'kspartner123');
    }

    private function createUsersForSomtel($manager, $customerServiceGroup, $userNames, $defaultPassword) {
        foreach( $userNames AS $userName ) {
            $this->createUserWithGroup($manager, $userName, $defaultPassword, $customerServiceGroup);
        }
    }

    private function createUsersForKsPartner($manager, $customerServiceGroup, $userNames, $defaultPassword) {
        foreach( $userNames AS $userName ) {
            $this->createUserWithGroup($manager, $userName, $defaultPassword, $customerServiceGroup);
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
