<?php

namespace Acme\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuizReply
 *
 * @ORM\Table(name="quiz_reply")
 * @ORM\Entity
 */
class QuizReply {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Acme\QuizBundle\Entity\Quiz
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Quiz", inversedBy="quizReplies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quiz_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $quiz;

    /**
     * @var \Acme\QuizBundle\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Question", inversedBy="quizReplies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $question;

    /**
     * @var \Acme\QuizBundle\Entity\WrongAnswer
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\WrongAnswer", inversedBy="quizReplies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wrong_given_answer_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $wrongGivenAnswer;
    
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
     * Set quiz
     *
     * @param \Acme\QuizBundle\Entity\Quiz
     * @return QuizReply
     */
    public function setQuiz(\Acme\QuizBundle\Entity\Quiz $quiz = null)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz
     *
     * @return \Acme\QuizBundle\Entity\Quiz
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * Set question
     *
     * @param \Acme\QuizBundle\Entity\Question
     * @return QuizReply
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
     * Set wrongGivenAnswer
     *
     * @param \Acme\QuizBundle\Entity\WrongAnswer
     * @return QuizReply
     */
    public function setWrongGivenAnswer(\Acme\QuizBundle\Entity\WrongAnswer $wrongGivenAnswer = null)
    {
        $this->wrongGivenAnswer = $wrongGivenAnswer;

        return $this;
    }

    /**
     * Get wrongGivenAnswer
     *
     * @return \Acme\QuizBundle\Entity\WrongAnswer
     */
    public function getWrongGivenAnswer()
    {
        return $this->question;
    }
}