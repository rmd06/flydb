<?php

namespace Fly\FlydbBundle\Entity;

use Fly\FlydbBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/** 
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $place;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $temperatureC;
    
    /**
     * @ORM\OneToMany(targetEntity="Flyline", mappedBy="location")
     */
    protected $flylines;
    
    public function __construct()
    {
        //parent::__construct();
        
        $this->flylines = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Location
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Location
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set temperatureC
     *
     * @param integer $temperatureC
     * @return Location
     */
    public function setTemperatureC($temperatureC)
    {
        $this->temperatureC = $temperatureC;
    
        return $this;
    }

    /**
     * Get temperatureC
     *
     * @return integer 
     */
    public function getTemperatureC()
    {
        return $this->temperatureC;
    }

    /**
     * Add flylines
     *
     * @param Fly\FlydbBundle\Entity\Flyline $flylines
     * @return Location
     */
    public function addFlyline(\Fly\FlydbBundle\Entity\Flyline $flylines)
    {
        $this->flylines[] = $flylines;
    
        return $this;
    }

    /**
     * Remove flylines
     *
     * @param Fly\FlydbBundle\Entity\Flyline $flylines
     */
    public function removeFlyline(\Fly\FlydbBundle\Entity\Flyline $flylines)
    {
        $this->flylines->removeElement($flylines);
    }

    /**
     * Get flylines
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFlylines()
    {
        return $this->flylines;
    }
}
