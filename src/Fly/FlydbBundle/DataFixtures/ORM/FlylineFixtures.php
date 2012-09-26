<?php

namespace Fly\FlydbBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Fly\FlydbBundle\Entity\Flyline;

class FlylineFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $userA = $manager->merge($this->getReference('user-a'));
        $userB = $manager->merge($this->getReference('user-b'));
        
        $location1 = $manager->merge($this->getReference('location1'));
        $location2 = $manager->merge($this->getReference('location2'));
        $location3 = $manager->merge($this->getReference('location3'));
        $location4 = $manager->merge($this->getReference('location4'));
        $location5 = $manager->merge($this->getReference('location5'));

        $flyline1 = new Flyline();
        $flyline1->setName('CS');
        $flyline1->setGenotype('+');
        $flyline1->setOrigin('nowhere');
        $flyline1->setTag('wt');
        $flyline1->setNote("Most common line in the world");
        $flyline1->setCreated(new \DateTime());
        $flyline1->setUpdated($flyline1->getCreated());
        $flyline1->setCared($flyline1->getCreated());
        $flyline1->setOwner($userA);
        $flyline1->setLocation($location1);
        $manager->persist($flyline1);
        
        $flyline2 = new Flyline();
        $flyline2->setName('WTB');
        $flyline2->setGenotype('+');
        $flyline2->setOrigin('nowhere');
        $flyline2->setTag('wt');
        $flyline2->setNote("Wild type Berlin");
        $flyline2->setCreated(new \DateTime());
        $flyline2->setUpdated($flyline2->getCreated());
        $flyline2->setCared($flyline2->getCreated());
        $flyline2->setOwner($userA);
        $flyline2->setLocation($location1);
        $manager->persist($flyline2);
        
        $flyline3 = new Flyline();
        $flyline3->setName('UAS-mGFP');
        $flyline3->setGenotype('+;UAS-mCD8::GFP;;');
        $flyline3->setOrigin('homemade');
        $flyline3->setTag('uas');
        $flyline3->setNote("A useful UAS line");
        $flyline3->setCreated(new \DateTime());
        $flyline3->setUpdated($flyline3->getCreated());
        $flyline3->setCared($flyline3->getCreated());
        $flyline3->setOwner($userB);
        $flyline3->setLocation($location2);
        $manager->persist($flyline3);
        
        $flyline4 = new Flyline();
        $flyline4->setName('OK107');
        $flyline4->setGenotype(';;;OK107-Gal4');
        $flyline4->setOrigin('somewhere');
        $flyline4->setTag('gal4');
        $flyline4->setNote("A very good GAL4 line");
        $flyline4->setCreated(new \DateTime());
        $flyline4->setUpdated($flyline4->getCreated());
        $flyline4->setCared($flyline4->getCreated());
        $flyline4->setOwner($userB);
        $flyline4->setLocation($location3);
        $manager->persist($flyline4);
                                
        $manager->flush();
        
        $this->addReference('flyline1', $flyline1);
        $this->addReference('flyline2', $flyline2);
        $this->addReference('flyline3', $flyline3);
        $this->addReference('flyline4', $flyline4);
        
        /**
         * add acls
         */
        $aclProvider = $this->container->get('security.acl.provider');
        $roleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');

        $objectIdentity1 = ObjectIdentity::fromDomainObject($flyline1);
        $acl1 = $aclProvider->createAcl($objectIdentity1);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($userA);
        $acl1->insertObjectAce($securityIdentity1, MaskBuilder::MASK_OWNER);
        $acl1->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_MASTER);
        $aclProvider->updateAcl($acl1);
        
        $objectIdentity2 = ObjectIdentity::fromDomainObject($flyline2);
        $acl2 = $aclProvider->createAcl($objectIdentity2);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($userA);
        $acl2->insertObjectAce($securityIdentity2, MaskBuilder::MASK_OWNER);
        $acl2->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_MASTER);
        $aclProvider->updateAcl($acl2);
        
        $objectIdentity3 = ObjectIdentity::fromDomainObject($flyline3);
        $acl3 = $aclProvider->createAcl($objectIdentity3);
        $securityIdentity3 = UserSecurityIdentity::fromAccount($userB);
        $acl3->insertObjectAce($securityIdentity3, MaskBuilder::MASK_OWNER);
        $acl3->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_MASTER);
        $aclProvider->updateAcl($acl3);
        
        $objectIdentity4 = ObjectIdentity::fromDomainObject($flyline4);
        $acl4 = $aclProvider->createAcl($objectIdentity4);
        $securityIdentity4 = UserSecurityIdentity::fromAccount($userB);
        $acl4->insertObjectAce($securityIdentity4, MaskBuilder::MASK_OWNER);
        $acl4->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_MASTER);
        $aclProvider->updateAcl($acl4);
        
    }
    
    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}
