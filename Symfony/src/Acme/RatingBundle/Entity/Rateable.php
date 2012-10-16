<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\Rateable
 *
 * @ORM\Table(name="rateable")
 * @ORM\Entity
 */
class Rateable
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string $typeName
     *
     * @ORM\Column(name="type_name", type="string", length=255, nullable=false)
     */
    private $typeName;

    /**
     * @var string $imageURL
     *
     * @ORM\Column(name="image_url", type="string", length=255, nullable=false)
     */
    private $imageURL;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

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
     * @ORM\ManyToOne(targetEntity="RateableCollection", inversedBy="rateables")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id")
     */
    private $collection;

    /**
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="rateable")
     */
    private $ratings;

    /**
     * @ORM\OneToOne(targetEntity="Acme\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="rateable_user_id", referencedColumnName="id")
     */
    private $rateableUser;


    public function __construct() 
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->isActive = TRUE;
        $this->ratings = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Rateable
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
     * Set typeName
     *
     * @param string $typeName
     * @return Rateable
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    
        return $this;
    }

    /**
     * Get typeName
     *
     * @return string 
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set imageURL
     *
     * @param string $imageURL
     * @return Rateable
     */
    public function setImageURL($imageURL)
    {
        $this->imageURL = $imageURL;
    
        return $this;
    }

    /**
     * Get imageURL
     *
     * @return string 
     */
    public function getImageURL()
    {
        return $this->imageURL;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Rateable
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Rateable
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
     * @return Rateable
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

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setCollection($collection)
    {
        if ( empty($this->collection) === FALSE )
            $this->collection->removeRateable($this);

        $collection->addRateable($this);
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function addRating($rating)
    {
        if ( $this->ratings->contains($rating) === FALSE )
            $this->ratings[] = $rating;
    }

    public function removeRating($rating)
    {
        if ( $this->ratings->contains($rating) === TRUE )
            $this->ratings->removeElement($rating);
    }

    public function getRatings()
    {
        return $this->ratings;
    }

    public function setRateableUser($user)
    {
        $this->rateableUser = $user;

        return $this;
    }

    public function getRateableUser()
    {
        return $this->rateableUser;
    }
}
