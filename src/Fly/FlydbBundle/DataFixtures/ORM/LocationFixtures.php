<?php

namespace Fly\FlydbBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fly\FlydbBundle\Entity\Location;

class LocationFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $location1 = new Location();
        $location1->setName('Main Culture Room');
        $location1->setPlace('303');
        $location1->setTemperatureC(25);
        $manager->persist($location1);
        
        $location2 = new Location();
        $location2->setName('Cool Culture Room');
        $location2->setPlace('303');
        $location2->setTemperatureC(18);
        $manager->persist($location2);
        
        $location3 = new Location();
        $location3->setName('303 Incubator 25');
        $location3->setPlace('303');
        $location3->setTemperatureC(25);
        $manager->persist($location3);
        
        $location4 = new Location();
        $location4->setName('308 Incubator 25');
        $location4->setPlace('308');
        $location4->setTemperatureC(25);
        $manager->persist($location4);
        
        $location5 = new Location();
        $location5->setName('308 Incubator 18');
        $location5->setPlace('308');
        $location5->setTemperatureC(18);
        $manager->persist($location5);
        
        $manager->flush();
        
        $this->addReference('location1', $location1);
        $this->addReference('location2', $location2);
        $this->addReference('location3', $location3);
        $this->addReference('location4', $location4);
        $this->addReference('location5', $location5);
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }    
}
