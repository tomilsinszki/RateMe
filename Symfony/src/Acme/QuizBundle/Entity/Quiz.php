<?php

namespace Acme\QuizBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Quiz
 *
 * @ORM\Table(name="quiz")
 * @ORM\Entity
 */
class Quiz {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \Acme\RatingBundle\Entity\Rateable
     *
     * @ORM\ManyToOne(targetEntity="Acme\RatingBundle\Entity\Rateable", inversedBy="quizzes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rateable_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $rateable;

    /**
     * @var \Acme\QuizBundle\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Question", inversedBy="quizzes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $question;

    /**
     * @var \Acme\QuizBundle\Entity\Answer
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Answer", inversedBy="quizzes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="given_answer_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $givenAnswer;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Quiz
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set rateable
     *
     * @param \Acme\RatingBundle\Entity\Rateable $rateable
     * @return Quiz
     */
    public function setRateable(\Acme\RatingBundle\Entity\Rateable $rateable = null)
    {
        $this->rateable = $rateable;

        return $this;
    }

    /**
     * Get rateable
     *
     * @return \Acme\RatingBundle\Entity\Rateable
     */
    public function getRateable()
    {
        return $this->rateable;
    }

    /**
     * Set question
     *
     * @param \Acme\QuizBundle\Entity\Question $question
     * @return Quiz
     */
    public function setQuestion(\Acme\QuizBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Acme\QuizBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set givenAnswer
     *
     * @param \Acme\QuizBundle\Entity\Answer $givenAnswer
     * @return Quiz
     */
    public function setGivenAnswer(\Acme\QuizBundle\Entity\Answer $givenAnswer = null)
    {
        $this->givenAnswer = $givenAnswer;

        return $this;
    }

    /**
     * Get givenAnswer
     *
     * @return \Acme\QuizBundle\Entity\Answer
     */
    public function getGivenAnswer()
    {
        return $this->givenAnswer;
    }
}