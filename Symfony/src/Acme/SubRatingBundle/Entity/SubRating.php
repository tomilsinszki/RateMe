<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\SubRatingBundle\Entity\SubRating
 *
 * @ORM\Table(name="sub_rating")
 * @ORM\Entity
 */
class SubRating
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
     * @ORM\ManyToOne(targetEntity="Answer", inversedBy="subRatings")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=false)
     */
    private $answer;
    
    /**
     * @ORM\ManyToOne(targetEntity="Acme\RatingBundle\Entity\Rating", inversedBy="subRatings")
     * @ORM\JoinColumn(name="rating_id", referencedColumnName="id", nullable=false)
     */
    private $rating;
    
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
    
    
    public function __construct() 
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * Set answer
     *
     * @param \Acme\SubRatingBundle\Entity\Answer $answer
     * @return SubRating
     */
    public function setAnswer(\Acme\SubRatingBundle\Entity\Answer $answer = null)
    {
        $this->answer = $answer;
    
        return $this;
    }

    /**
     * Get answer
     *
     * @return \Acme\SubRatingBundle\Entity\Answer
     */
    public function getAnswer()
    {
        return $this->answer;
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
     * @return Answer
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
    
    /**
     * Set rating
     *
     * @param \Acme\RatingBundle\Entity\Rating $rating
     * @return SubRating
     */
    public function setRating(\Acme\RatingBundle\Entity\Rating $rating = null)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return \Acme\RatingBundle\Entity\Rating
     */
    public function getRating()
    {
        return $this->rating;
    }
}
