<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Acme\SubRatingBundle\Entity\Question
 *
 * @ORM\Entity
 * @ORM\Table(name="sub_rating_question", uniqueConstraints={@ORM\UniqueConstraint(columns={"sequence", "collection_id"})})
 */
class Question
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
     * @var integer $sequence
     * 
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private $sequence;
    
    /**
     * @var string $title
     * 
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;
    
    /**
     * @var text $text
     * 
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     */
    private $text;
    
    /**
     * @var integer $target
     * 
     * @ORM\Column(name="target", type="integer", nullable=false)
     */
    private $target;
    
    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")     
     */
    private $answers;
    
    /**
     * @ORM\ManyToOne(targetEntity="QuestionType", inversedBy="questions")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
     */
    private $questionType;

    /**
     * @ORM\ManyToOne(targetEntity="Acme\RatingBundle\Entity\RateableCollection", inversedBy="questions")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=false)
     */
    private $rateableCollection;

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
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Question
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Question
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
     * Set sequence
     *
     * @param integer $sequence
     * @return Question
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    
        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set target
     *
     * @param integer $target
     * @return Question
     */
    public function setTarget($target)
    {
        $this->target = $target;
    
        return $this;
    }

    /**
     * Get target
     *
     * @return integer 
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Question
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
     * @return Question
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
     * @return Question
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
     * Add answer
     *
     * @param \Acme\SubRatingBundle\Entity\Answer $answer
     * @return Question
     */
    public function addAnswer(\Acme\SubRatingBundle\Entity\Answer $answer)
    {
        $this->answers[] = $answer;
    
        return $this;
    }

    /**
     * Remove answer
     *
     * @param \Acme\SubRatingBundle\Entity\Answer $answer
     */
    public function removeAnswer(\Acme\SubRatingBundle\Entity\Answer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }
    
    /**
     * Set questionType
     *
     * @param \Acme\SubRatingBundle\Entity\QuestionType $questionType
     * @return Question
     */
    public function setQuestionType(\Acme\SubRatingBundle\Entity\QuestionType $questionType = null)
    {
        $this->questionType = $questionType;
    
        return $this;
    }
    
    /**
     * Get questionType
     *
     * @return \Acme\SubRatingBundle\Entity\QuestionType
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }
    
    /**
     * Set rateableCollection
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection $rateableCollection
     * @return Question
     */
    public function setRateableCollection(\Acme\RatingBundle\Entity\RateableCollection $rateableCollection = null)
    {
        $this->rateableCollection = $rateableCollection;
    
        return $this;
    }

    /**
     * Get rateableCollection
     *
     * @return \Acme\RatingBundle\Entity\RateableCollection
     */
    public function getRateableCollection()
    {
        return $this->rateableCollection;
    }
}

