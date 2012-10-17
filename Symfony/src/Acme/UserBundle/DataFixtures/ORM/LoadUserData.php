<?php

namespace Acme\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Acme\UserBundle\Entity\User;
use Acme\UserBundle\Entity\Role;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $role = new Role();
        $role->setName('ROLE_RATER');

        $user = new User();

        $user->setUsername('test');
        $user->setEmail('test@test.com');

        $user->setSalt(md5(time()));

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword('test', $user->getSalt());
        $user->setPassword($password);

        $user->addRole($role);

        $manager->persist($role);
        $manager->persist($user);
        $manager->flush();
    }
}
