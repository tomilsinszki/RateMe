<?php

namespace Acme\QuizBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Question
 *
 * @ORM\Table(name="question",
 * uniqueConstraints={@ORM\UniqueConstraint(name="unique_question",columns={"text", "rateable_collection_id"})})
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
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="correct_answer_text", type="string", length=255, nullable=false)
     */
    private $correctAnswerText;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WrongAnswer", mappedBy="question")
     */
    private $wrongAnswers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizReply", mappedBy="question")
     */
    private $quizReplies;

    /**
     * @var \Acme\RatingBundle\Entity\RateableCollection
     *
     * @ORM\ManyToOne(targetEntity="Acme\RatingBundle\Entity\RateableCollection", inversedBy="questions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rateable_collection_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $rateableCollection;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;

    public function __construct() {
        $this->wrongAnswers = new ArrayCollection();
        $this->quizReplies = new ArrayCollection();
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
     * Set correctAnswerText
     *
     * @param string $correctAnswerText
     * @return Question
     */
    public function setCorrectAnswerText($correctAnswerText)
    {
        $this->correctAnswerText = $correctAnswerText;

        return $this;
    }

    /**
     * Get correctAnswerText
     *
     * @return string
     */
    public function getCorrectAnswerText()
    {
        return $this->correctAnswerText;
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

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function logDeleted($withWrongAnswersAlso = true) {
        $this->setDeleted(new \DateTime('now'));

        if ($withWrongAnswersAlso) {
            foreach ($this->getWrongAnswers() as $wrongAnswer) {
                $wrongAnswer->logDeleted();
            }
        }

        return $this;
    }

    public function logUnDeleted($withWrongAnswersAlso = true) {
        $this->setDeleted(null);

        if ($withWrongAnswersAlso) {
            foreach ($this->getWrongAnswers() as $wrongAnswer) {
                $wrongAnswer->logUnDeleted();
            }
        }

        return $this;
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
     * Set rateableCollection
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection
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

    /**
     * Add wrongAnswer
     *
     * @param \Acme\QuizBundle\Entity\WrongAnswer $wrongAnswer
     * @return Question
     */
    public function addWrongAnswer(\Acme\QuizBundle\Entity\WrongAnswer $wrongAnswer)
    {
        $this->wrongAnswers[] = $wrongAnswer;

        return $this;
    }

    /**
     * Remove wrongAnswer
     *
     * @param \Acme\QuizBundle\Entity\WrongAnswer $wrongAnswer
     */
    public function removeWrongAnswer(\Acme\QuizBundle\Entity\WrongAnswer $wrongAnswer)
    {
        $this->wrongAnswers->removeElement($wrongAnswers);
    }

    /**
     * Get wrongAnswers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWrongAnswers()
    {
        return $this->wrongAnswers;
    }

    /**
     * Add quizReply
     *
     * @param \Acme\QuizBundle\Entity\QuizReply $quizReply
     * @return Question
     */
    public function addQuizReply(\Acme\QuizBundle\Entity\QuizReply $quizReply)
    {
        $this->quizReplies[] = $quizReply;

        return $this;
    }

    /**
     * Remove quizReply
     *
     * @param \Acme\QuizBundle\Entity\QuizReply $quizReply
     */
    public function removeQuizReply(\Acme\QuizBundle\Entity\QuizReply $quizReply)
    {
        $this->quizReplies->removeElement($quizReply);
    }

    /**
     * Get quizReplies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuizReplies()
    {
        return $this->quizReplies;
    }
}