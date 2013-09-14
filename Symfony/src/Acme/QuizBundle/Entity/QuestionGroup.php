<?php

namespace Acme\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionGroup
 *
 * @ORM\Table(name="question_group")
 * @ORM\Entity
 */
class QuestionGroup {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="group")
     */
    private $questions;

    public function __construct() {
        $this->questions = new ArrayCollection();
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
     * @return QuestionGroup
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
     * Add questions
     *
     * @param \Acme\QuizBundle\Entity\Question $questions
     * @return QuestionGroup
     */
    public function addQuestion(\Acme\QuizBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;
    
        return $this;
    }

    /**
     * Remove questions
     *
     * @param \Acme\QuizBundle\Entity\Question $questions
     */
    public function removeQuestion(\Acme\QuizBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
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