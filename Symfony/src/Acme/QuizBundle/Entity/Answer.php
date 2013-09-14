<?php

namespace Acme\QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="answer")
 * @ORM\Entity
 */
class Answer
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
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     */
    private $text;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="correctAnswer")
     */
    private $questions_related_by_correct_answer;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="wrongAnswer1")
     */
    private $questions_related_by_wrong_answer1;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="wrongAnswer2")
     */
    private $questions_related_by_wrong_answer2;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Quiz", mappedBy="givenAnswer")
     */
    private $quizzes;

    public function __construct() {
        $this->questions_related_by_correct_answer = new ArrayCollection();
        $this->questions_related_by_wrong_answer1 = new ArrayCollection();
        $this->questions_related_by_wrong_answer2 = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
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
     * Add questions_related_by_correct_answer
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByCorrectAnswer
     * @return Answer
     */
    public function addQuestionsRelatedByCorrectAnswer(\Acme\QuizBundle\Entity\Question $questionsRelatedByCorrectAnswer)
    {
        $this->questions_related_by_correct_answer[] = $questionsRelatedByCorrectAnswer;
    
        return $this;
    }

    /**
     * Remove questions_related_by_correct_answer
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByCorrectAnswer
     */
    public function removeQuestionsRelatedByCorrectAnswer(\Acme\QuizBundle\Entity\Question $questionsRelatedByCorrectAnswer)
    {
        $this->questions_related_by_correct_answer->removeElement($questionsRelatedByCorrectAnswer);
    }

    /**
     * Get questions_related_by_correct_answer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionsRelatedByCorrectAnswer()
    {
        return $this->questions_related_by_correct_answer;
    }

    /**
     * Add questions_related_by_wrong_answer1
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer1
     * @return Answer
     */
    public function addQuestionsRelatedByWrongAnswer1(\Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer1)
    {
        $this->questions_related_by_wrong_answer1[] = $questionsRelatedByWrongAnswer1;
    
        return $this;
    }

    /**
     * Remove questions_related_by_wrong_answer1
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer1
     */
    public function removeQuestionsRelatedByWrongAnswer1(\Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer1)
    {
        $this->questions_related_by_wrong_answer1->removeElement($questionsRelatedByWrongAnswer1);
    }

    /**
     * Get questions_related_by_wrong_answer1
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionsRelatedByWrongAnswer1()
    {
        return $this->questions_related_by_wrong_answer1;
    }

    /**
     * Add questions_related_by_wrong_answer2
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer2
     * @return Answer
     */
    public function addQuestionsRelatedByWrongAnswer2(\Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer2)
    {
        $this->questions_related_by_wrong_answer2[] = $questionsRelatedByWrongAnswer2;
    
        return $this;
    }

    /**
     * Remove questions_related_by_wrong_answer2
     *
     * @param \Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer2
     */
    public function removeQuestionsRelatedByWrongAnswer2(\Acme\QuizBundle\Entity\Question $questionsRelatedByWrongAnswer2)
    {
        $this->questions_related_by_wrong_answer2->removeElement($questionsRelatedByWrongAnswer2);
    }

    /**
     * Get questions_related_by_wrong_answer2
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionsRelatedByWrongAnswer2()
    {
        return $this->questions_related_by_wrong_answer2;
    }

    /**
     * Add quizzes
     *
     * @param \Acme\QuizBundle\Entity\Quiz $quizzes
     * @return Answer
     */
    public function addQuizze(\Acme\QuizBundle\Entity\Quiz $quizzes)
    {
        $this->quizzes[] = $quizzes;
    
        return $this;
    }

    /**
     * Remove quizzes
     *
     * @param \Acme\QuizBundle\Entity\Quiz $quizzes
     */
    public function removeQuizze(\Acme\QuizBundle\Entity\Quiz $quizzes)
    {
        $this->quizzes->removeElement($quizzes);
    }

    /**
     * Get quizzes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuizzes()
    {
        return $this->quizzes;
    }
}