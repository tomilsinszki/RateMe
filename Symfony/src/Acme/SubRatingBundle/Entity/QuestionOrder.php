<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\SubRatingBundle\Entity\QuestionOrder
 *
 * @ORM\Table(name="sub_rating_question_order")
 * @ORM\Entity
 */
class QuestionOrder
{
    /**
     * @var integer $id
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
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
     * @ORM\OneToMany(targetEntity="Acme\RatingBundle\Entity\RateableCollection", mappedBy="questionOrder")
     */
    private $collections;
    
    
    public function __construct() 
    {
        $this->collections = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return QuestionListType
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
     * Add rateableCollection
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection $rateableCollection
     * @return QuestionOrder
     */
    public function addRateableCollection(\Acme\RatingBundle\Entity\RateableCollection $rateableCollection)
    {
        $this->collections[] = $rateableCollection;
    
        return $this;
    }

    /**
     * Remove rateableCollection
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection $rateableCollection
     */
    public function removeRateableCollection(\Acme\RatingBundle\Entity\RateableCollection $rateableCollection)
    {
        $this->collections->removeElement($rateableCollection);
    }

    /**
     * Get collections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollections()
    {
        return $this->collections;
    }
}
