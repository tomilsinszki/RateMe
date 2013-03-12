<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\UserBundle\Entity\ReadEmail
 *
 * @ORM\Table(name="read_email")
 * @ORM\Entity
 */
class ReadEmail
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=511)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="readEmails")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setUser($user)
    {
        if ( empty($this->user) === FALSE ) {
            $this->user->removeReadEmail($this);
        }
   
        $user->addReadEmail($this);
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

}
