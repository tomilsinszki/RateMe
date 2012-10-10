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
     * @var string $type_name
     *
     * @ORM\Column(name="type_name", type="string", length=255, nullable=false)
     */
    private $type_name;

    /**
     * @var string $image_url
     *
     * @ORM\Column(name="image_url", type="string", length=255, nullable=false)
     */
    private $image_url;

    /**
     * @var boolean $is_active
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $is_active;

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


    public function __construct() {
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
     * Set type_name
     *
     * @param string $typeName
     * @return Rateable
     */
    public function setTypeName($typeName)
    {
        $this->type_name = $typeName;
    
        return $this;
    }

    /**
     * Get type_name
     *
     * @return string 
     */
    public function getTypeName()
    {
        return $this->type_name;
    }

    /**
     * Set image_url
     *
     * @param string $imageUrl
     * @return Rateable
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;
    
        return $this;
    }

    /**
     * Get image_url
     *
     * @return string 
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Rateable
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
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
}
