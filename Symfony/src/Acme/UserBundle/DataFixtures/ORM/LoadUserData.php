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
        $group = new Group();
        $group->setName('rater');
        $group->setRole('ROLE_RATER');

        $user = new User();

        $user->setUsername('test@test.com');
        $user->setEmail('test@test.com');

        $user->setSalt(md5(time()));

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword('test', $user->getSalt());
        $user->setPassword($password);

        $user->addGroup($group);

        $manager->persist($group);
        $manager->persist($user);
        $manager->flush();
    }
}
