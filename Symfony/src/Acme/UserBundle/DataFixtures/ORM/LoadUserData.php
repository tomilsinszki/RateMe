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
        
        //$this->createUserWithGroup($manager, 'rater@rater.com', 'rater', $raterGroup);
        $this->createUserWithGroup($manager, 'manager@manager.com', 'manager', $managerGroup);
        //$this->createUserWithGroup($manager, 'cs@cs.com', 'cuse', $customerServiceGroup);
        
        $this->createUserWithGroup($manager, 'barko.renata', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'gozo.viktoria', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'jarfas.nora', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'jordanics.julianna', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'kocsis.zsuzsanna', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'kovacs.marta', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'kutasi.krisztina', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'lapat.krisztina', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'megla.dora', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'mocsan.dea', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'molnar.edina', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'molnar.timea', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'nagyne.balogh.tunde', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'nemethne.kaman.maria', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'reveszne.borsos.anita', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'szucs.agnes', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'valint.sarolt', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'siposne.deak.timea', 'somtel123', $customerServiceGroup);
        $this->createUserWithGroup($manager, 'kotfas.judit', 'somtel123', $customerServiceGroup);
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
