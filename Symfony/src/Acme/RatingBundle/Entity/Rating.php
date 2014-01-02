<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\Rating
 *
 * @ORM\Table(name="rating")
 * @ORM\Entity
 */
class Rating
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
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;
    
    /**
     * @var string $ratingIpAddress
     *
     * @ORM\Column(name="rating_ip_address", type="string", length=255, nullable=true)
     */
    private $ratingIpAddress;
    
    /**
     * @var integer $stars
     *
     * @ORM\Column(name="stars", type="integer", nullable=false)
     */
    private $stars;

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
     * @ORM\ManyToOne(targetEntity="Rateable", inversedBy="ratings")
     * @ORM\JoinColumn(name="rateable_id", referencedColumnName="id")
     */
    private $rateable;

    /**
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="rating_user_id", referencedColumnName="id")
     */
    private $ratingUser;
    
    /**
     * @ORM\OneToMany(targetEntity="Acme\SubRatingBundle\Entity\SubRating", mappedBy="rating")
     */
    private $subRatings;


    public function __construct() 
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->subRatings = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     * @return Rating
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set ratingIpAddress
     *
     * @param string $ratingIpAddress
     * @return Rating
     */
    public function setRatingIpAddress($ratingIpAddress)
    {
        $this->ratingIpAddress = $ratingIpAddress;
    
        return $this;
    }

    /**
     * Get ratingIpAddress
     *
     * @return string 
     */
    public function getRatingIpAddress()
    {
        return $this->ratingIpAddress;
    }

    /**
     * Set stars
     *
     * @param integer $stars
     * @return Rating
     */
    public function setStars($stars)
    {
        $this->stars = $stars;
    
        return $this;
    }

    /**
     * Get stars
     *
     * @return integer 
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Rating
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
     * @return Rating
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

    public function setRateable($rateable)
    {
        if ( empty($this->rateable) === FALSE )
            $this->rateable->removeRating($this);

        $rateable->addRating($this);
        $this->rateable = $rateable;
    }

    public function getRateable()
    {
        return $this->rateable;
    }

    public function setRatingUser($user)
    {
        $this->ratingUser = $user;

        return $this;
    }

    public function getRatingUser()
    {
        return $this->ratingUser;
    }

    /**
     * Add subRating
     *
     * @param \Acme\SubRatingBundle\Entity\SubRating $subRating
     * @return Rating
     */
    public function addSubRating(\Acme\SubRatingBundle\Entity\SubRating $subRating)
    {
        $this->subRatings[] = $subRating;
    
        return $this;
    }

    /**
     * Remove subRating
     *
     * @param \Acme\SubRatingBundle\Entity\SubRating $subRating
     */
    public function removeSubRating(\Acme\SubRatingBundle\Entity\SubRating $subRating)
    {
        $this->subRatings->removeElement($subRating);
    }

    /**
     * Get subRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubRatings()
    {
        return $this->subRatings;
    }
}
