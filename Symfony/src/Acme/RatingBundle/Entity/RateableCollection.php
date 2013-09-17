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
     * @var string $foreignURL
     *
     * @ORM\Column(name="foreign_url", type="string", length=255, nullable=true)
     */
    private $foreignURL;

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
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="Rateable", mappedBy="collection")
     */
    private $rateables;

    /**
     * @ORM\ManyToMany(targetEntity="Acme\UserBundle\Entity\User", inversedBy="ownedCollections")
     * @ORM\JoinTable(name="rateable_collection_owner",
     *      joinColumns={@ORM\JoinColumn(name="collection_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $owners;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="rateableCollections")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;
    

    public function __construct() 
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * Set foreignURL
     *
     * @param string $foreignURL
     * @return RateableCollection
     */
    public function setForeignURL($foreignURL)
    {
        $this->foreignURL = $foreignURL;
    
        return $this;
    }

    /**
     * Get foreignURL
     *
     * @return string 
     */
    public function getForeignURL()
    {
        return $this->foreignURL;
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

    public function logUpdated()
    {
        $this->updated = new \DateTime("now");
    
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

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getRateables()
    {
        return $this->rateables;
    }

    public function addRateable($rateable)
    {
        if ( $this->rateables->contains($rateable) === FALSE )
            $this->rateables[] = $rateable;
    }

    public function removeRateable($rateable)
    {
        if ( $this->rateables->contains($rateable) === TRUE )
            $this->rateables->removeElement($rateable);
    }

    public function addOwner($user)
    {
        if ( $this->owners->contains($user) === FALSE )
            $this->owners[] = $user;
    }

    public function removeOwner($user)
    {
        if ( $this->owners->contains($user) === TRUE )
            $this->owners->removeElement($user);
    }

    public function setCompany($company)
    {
        if ( empty($this->company) === FALSE ) {
            $this->company->removeRateableCollection($this);
        }

        $company->addRateableCollection($this);
        $this->company = $company;
    }

    public function getCompany()
    {
        return $this->company;
    }
}
