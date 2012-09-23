<?php

namespace Fly\FlydbBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fly\FlydbBundle\Entity\User;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        
        $userAdmin = $userManager->createUser();
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@example.com');
        $userAdmin->setEnabled(1);
        $userAdmin->setSuperAdmin(1);
        $userAdmin->addRole('ROLE_ADMIN');
        $encoderAdmin = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userAdmin)
        ;
        $userAdmin->setPassword($encoderAdmin->encodePassword('admin', $userAdmin->getSalt()));
        $userManager->updateUser($userAdmin);
        
        $userA = $userManager->createUser();
        $userA->setUsername('alice');
        $userA->setEmail('alice@example.com');
        $userA->setEnabled(1);
        $userA->addRole('ROLE_USER');
        $encoderA = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userA)
        ;
        $userA->setPassword($encoderA->encodePassword('alice', $userA->getSalt()));
        $userManager->updateUser($userA);

        $userB = $userManager->createUser();
        $userB->setUsername('ben');
        $userB->setEmail('ben@example.com');
        $userB->setEnabled(1);
        $userB->addRole('ROLE_USER');
        $encoderB = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userB)
        ;
        $userB->setPassword($encoderB->encodePassword('ben', $userB->getSalt()));
        $userManager->updateUser($userB);

        $manager->flush();

        $this->addReference('admin-user', $userAdmin);
        $this->addReference('user-a', $userA);
        $this->addReference('user-b', $userB);
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }    
}
