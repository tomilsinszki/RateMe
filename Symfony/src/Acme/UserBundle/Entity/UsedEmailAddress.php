<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\UserBundle\Entity\UsedEmailAddress
 *
 * @ORM\Table(name="used_email_address")
 * @ORM\Entity
 */
class UsedEmailAddress
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
     * @var string $emailAddress
     *
     * @ORM\Column(name="email_address", type="string", length=511)
     */
    private $emailAddress;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="usedEmailAddresses")
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
     * Set email address
     *
     * @param string $emailAddress
     * @return UsedEmailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function setUser($user)
    {
        if ( empty($this->user) === FALSE ) {
            $this->user->removeUsedEmailAddress($this);
        }
   
        $user->addUsedEmailAddress($this);
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

}
