<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\RateableCollection
 *
 * @ORM\Table(name="rateable_collection")
 * @ORM\Entity
 */
class RateableCollection
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     * @var string $foreignUrl
     *
     * @ORM\Column(name="foreign_url", type="string", length=255, nullable=true)
     */
    private $foreignUrl;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @ORM\OneToOne(targetEntity="Identifier")
     * @ORM\JoinColumn(name="identifier_id", referencedColumnName="id")
     */
    private $identifier;

    /**
     * @ORM\OneToMany(targetEntity="Rateable", mappedBy="collection")
     */
    private $rateables;

    /**
     * @ORM\ManyToMany(targetEntity="Acme\UserBundle\Entity\User")
     * @ORM\JoinTable(name="rateable_collection_owner",
     *      joinColumns={@ORM\JoinColumn(name="collection_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     *      )
     */
     private $owners;


    public function __construct() {
        $this->rateables = new \Doctrine\Common\Collections\ArrayCollection();
        $this->owners = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RateableCollection
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
     * Set foreignUrl
     *
     * @param string $foreignUrl
     * @return RateableCollection
     */
    public function setForeignUrl($foreignUrl)
    {
        $this->foreignUrl = $foreignUrl;
    
        return $this;
    }

    /**
     * Get foreignUrl
     *
     * @return string 
     */
    public function getForeignUrl()
    {
        return $this->foreignUrl;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RateableCollection
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return RateableCollection
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
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
}
