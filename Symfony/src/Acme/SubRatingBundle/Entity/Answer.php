<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\SubRatingBundle\Entity\Answer
 *
 * @ORM\Table(name="sub_rating_answer")
 * @ORM\Entity
 */
class Answer
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
     * @var text $text
     * 
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;
    
    /**
     * @var isEnabled $isEnabled
     * 
     * @ORM\Column(name="is_enabled", type="boolean", nullable=false)
     */
    private $isEnabled;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="AnswerType", inversedBy="answers")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $answerType;
    
    /**
     * @ORM\OneToMany(targetEntity="SubRating", mappedBy="answer")
     */
    private $subRatings;

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
     * @var \DateTime $deleted
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isEnabled = true;
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
     * Set text
     *
     * @param string $text
     * @return Answer
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return Answer
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    
        return $this;
    }
    
    /**
     * Get isEnabled
     *
     * @return boolean 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }
    
    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Answer
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
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return Answer
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    public function logDeleted()
    {
        $this->deleted = new \DateTime("now");
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set question
     *
     * @param \Acme\SubRatingBundle\Entity\Question $question
     * @return Answer
     */
    public function setQuestion(\Acme\SubRatingBundle\Entity\Question $question = null)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return \Acme\SubRatingBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answerType
     *
     * @param \Acme\SubRatingBundle\Entity\AnswerType $answerType
     * @return Answer
     */
    public function setAnswerType(\Acme\SubRatingBundle\Entity\AnswerType $answerType = null)
    {
        $this->answerType = $answerType;
    
        return $this;
    }

    /**
     * Get answerType
     *
     * @return \Acme\SubRatingBundle\Entity\AnswerType
     */
    public function getAnswerType()
    {
        return $this->answerType;
    }
    
    /**
     * Add subRating
     *
     * @param \Acme\SubRatingBundle\Entity\SubRating $subRating
     * @return Answer
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

