<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\SubRatingBundle\Entity\AnswerType
 *
 * @ORM\Table(name="sub_rating_answer_type")
 * @ORM\Entity
 */
class AnswerType
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
     * @ORM\ManyToOne(targetEntity="QuestionType", inversedBy="answerTypes")
     * @ORM\JoinColumn(name="question_type_id", referencedColumnName="id", nullable=false)
     */
    private $questionType;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="answerType")
     */
    private $answers;
    
    
    public function __construct() 
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return AnswerType
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
     * Set questionType
     *
     * @param \Acme\SubRatingBundle\Entity\QuestionType $questionType
     * @return AnswerType
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
     * Add answer
     *
     * @param \Acme\SubRatingBundle\Entity\Answer $answer
     * @return AnswerType
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
}
