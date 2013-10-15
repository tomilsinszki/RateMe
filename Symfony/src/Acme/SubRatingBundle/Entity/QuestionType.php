<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\SubRatingBundle\Entity\QuestionType
 *
 * @ORM\Table(name="sub_rating_question_type")
 * @ORM\Entity
 */
class QuestionType
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
     * @ORM\OneToMany(targetEntity="AnswerType", mappedBy="questionType")
     */
    private $answerTypes;

    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="questionType")
     */
    private $questions;

    
    
    public function __construct() 
    {
        $this->answerTypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return QuestionType
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
     * Add answerType
     *
     * @param \Acme\SubRatingBundle\Entity\AnswerType $answerType
     * @return QuestionType
     */
    public function addAnswerType(\Acme\SubRatingBundle\Entity\AnswerType $answerType)
    {
        $this->answerTypes[] = $answerType;
    
        return $this;
    }

    /**
     * Remove answerType
     *
     * @param \Acme\SubRatingBundle\Entity\AnswerType $answerType
     */
    public function removeAnswerType(\Acme\SubRatingBundle\Entity\AnswerType $answerType)
    {
        $this->answerTypes->removeElement($answerType);
    }

    /**
     * Get answerTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswerTypes()
    {
        return $this->answerTypes;
    }

    /**
     * Add question
     *
     * @param \Acme\SubRatingBundle\Entity\Question $question
     * @return QuestionType
     */
    public function addQuestion(\Acme\SubRatingBundle\Entity\Question $question)
    {
        $this->questions[] = $question;
    
        return $this;
    }

    /**
     * Remove question
     *
     * @param \Acme\SubRatingBundle\Entity\Question $question
     */
    public function removeQuestion(\Acme\SubRatingBundle\Entity\Question $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}

