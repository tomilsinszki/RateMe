<?php

namespace Acme\QuizBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * WrongAnswer
 *
 * @ORM\Table(name="quiz_wrong_answer",
 * uniqueConstraints={@ORM\UniqueConstraint(name="unique_wrong_answer",columns={"text", "question_id"})})
 * @ORM\Entity
 */
class WrongAnswer
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
     * @var \Acme\QuizBundle\Entity\Question
     *
     * @ORM\ManyToOne(targetEntity="Acme\QuizBundle\Entity\Question", inversedBy="wrongAnswers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $question;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizReply", mappedBy="wrongAnswer")
     */
    private $quizReplies;

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
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted;

    public function __construct() {
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
     * Set question
     *
     * @param \Acme\QuizBundle\Entity\Question
     * @return WrongAnswer
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
     * Add quizReply
     *
     * @param \Acme\QuizBundle\Entity\QuizReply $quizReply
     * @return WrongAnswer
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

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return WrongAnswer
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

    public function logDeleted() {
        $this->setDeleted(new \DateTime('now'));

        return $this;
    }

    public function logUnDeleted() {
        $this->setDeleted(null);

        return $this;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return WrongAnswer
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
}
