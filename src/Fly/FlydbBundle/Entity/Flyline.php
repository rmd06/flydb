<?php

namespace Fly\FlydbBundle\Entity;

use Fly\FlydbBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Fly\FlydbBundle\Repository\FlylineRepository")
 * @ORM\Table(name="flyline")
 * @ORM\HasLifecycleCallbacks()
 */
class Flyline
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
    protected $genotype;
    
    protected $location;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $cared;
    
    
    /**
     * @ORM\Column(type="string")
     */
    protected $origin;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $tag;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $note;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="flylines")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;
    
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
     * Set genotype
     *
     * @param string $genotype
     * @return Flyline
     */
    public function setGenotype($genotype)
    {
        $this->genotype = $genotype;
    
        return $this;
    }

    /**
     * Get genotype
     *
     * @return string 
     */
    public function getGenotype()
    {
        return $this->genotype;
    }

    /**
     * Set origin
     *
     * @param string $origin
     * @return Flyline
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    
        return $this;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Flyline
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Flyline
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote($length = null)
    {
        if (false === is_null($length) && $length > 0)
            return substr($this->note, 0, $length);
        else
            return $this->note;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateTimePrePersist()
    {
        $newDateTime = new \DateTime();
        $this->created = $newDateTime;
        $this->updated = $newDateTime;
        $this->cared = $newDateTime;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Flyline
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Flyline
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Flyline
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
     * Set owner
     *
     * @param Fly\FlydbBundle\Entity\User $owner
     * @return Flyline
     */
    public function setOwner(\Fly\FlydbBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return Fly\FlydbBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set cared
     *
     * @param \DateTime $cared
     * @return Flyline
     */
    public function setCared($cared)
    {
        $this->cared = $cared;
    
        return $this;
    }

    /**
     * Get cared
     *
     * @return \DateTime 
     */
    public function getCared()
    {
        return $this->cared;
    }
}
