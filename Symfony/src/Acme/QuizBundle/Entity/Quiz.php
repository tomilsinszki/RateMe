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
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \Acme\RatingBundle\Entity\Rateable
     *
     * @ORM\ManyToOne(targetEntity="Acme\RatingBundle\Entity\Rateable", inversedBy="quizzes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rateable_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $rateable;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizReply", mappedBy="quiz")
     */
    private $quizReplies;

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
     * Set created
     *
     * @param \DateTime $created
     * @return Quiz
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
     * Add quizReply
     *
     * @param \Acme\QuizBundle\Entity\QuizReply $quizReply
     * @return Quiz
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