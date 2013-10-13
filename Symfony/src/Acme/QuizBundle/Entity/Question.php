<?php

namespace Acme\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Acme\QuizBundle\Entity\QuestionRepository")
 */
class Question
{
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
     * @ORM\Column(name="text", type="string", length=255, nullable=false, unique=true)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_occured_at", type="datetime", nullable=true)
     */
    private $lastOccuredAt;

    /**
     * @var \Acme\QuizBundle\Entity\QuestionGroup
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\QuestionGroup", inversedBy="questions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $group;

    /**
     * @var \Acme\QuizBundle\Entity\Answer
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Answer", inversedBy="questions_related_by_correct_answer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="correct_answer_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $correctAnswer;

    /**
     * @var \Acme\QuizBundle\Entity\Answer
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Answer", inversedBy="questions_related_by_wrong_answer1")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wrong_answer1_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $wrongAnswer1;

    /**
     * @var \Acme\QuizBundle\Entity\Answer
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Answer", inversedBy="questions_related_by_wrong_answer2")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wrong_answer2_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $wrongAnswer2;


    public function logOccured() {
        $this->setLastOccuredAt(new \DateTime('now'));

        return $this;
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
     * Set lastOccuredAt
     *
     * @param \DateTime $lastOccuredAt
     * @return Question
     */
    public function setLastOccuredAt($lastOccuredAt)
    {
        $this->lastOccuredAt = $lastOccuredAt;

        return $this;
    }

    /**
     * Get lastOccuredAt
     *
     * @return \DateTime
     */
    public function getLastOccuredAt()
    {
        return $this->lastOccuredAt;
    }

    /**
     * Set group
     *
     * @param \Acme\QuizBundle\Entity\QuestionGroup $group
     * @return Question
     */
    public function setGroup(\Acme\QuizBundle\Entity\QuestionGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Acme\QuizBundle\Entity\QuestionGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set correctAnswer
     *
     * @param \Acme\QuizBundle\Entity\Answer $correctAnswer
     * @return Question
     */
    public function setCorrectAnswer(\Acme\QuizBundle\Entity\Answer $correctAnswer = null)
    {
        $this->correctAnswer = $correctAnswer;

        return $this;
    }

    /**
     * Get correctAnswer
     *
     * @return \Acme\QuizBundle\Entity\Answer
     */
    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }

    /**
     * Set wrongAnswer1
     *
     * @param \Acme\QuizBundle\Entity\Answer $wrongAnswer1
     * @return Question
     */
    public function setWrongAnswer1(\Acme\QuizBundle\Entity\Answer $wrongAnswer1 = null)
    {
        $this->wrongAnswer1 = $wrongAnswer1;

        return $this;
    }

    /**
     * Get wrongAnswer1
     *
     * @return \Acme\QuizBundle\Entity\Answer
     */
    public function getWrongAnswer1()
    {
        return $this->wrongAnswer1;
    }

    /**
     * Set wrongAnswer2
     *
     * @param \Acme\QuizBundle\Entity\Answer $wrongAnswer2
     * @return Question
     */
    public function setWrongAnswer2(\Acme\QuizBundle\Entity\Answer $wrongAnswer2 = null)
    {
        $this->wrongAnswer2 = $wrongAnswer2;

        return $this;
    }

    /**
     * Get wrongAnswer2
     *
     * @return \Acme\QuizBundle\Entity\Answer
     */
    public function getWrongAnswer2()
    {
        return $this->wrongAnswer2;
    }
}