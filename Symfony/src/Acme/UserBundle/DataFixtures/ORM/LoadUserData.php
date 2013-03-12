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

        $raterUser = $this->createUserWithGroup($manager, 'rater@rater.com', 'rater', $raterGroup);
        $raterUser = $this->createUserWithGroup($manager, 'manager@manager.com', 'manager', $managerGroup);
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
