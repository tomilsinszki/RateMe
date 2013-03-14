<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Acme\RatingBundle\Entity\Client
 *
 * @ORM\Table(name="client", uniqueConstraints={@ORM\UniqueConstraint(columns={"client_id", "company_id"})})
 * @ORM\Entity
 */
class Client
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
     * @var string $clientId
     *
     * @ORM\Column(name="client_id", type="string", length=255)
     */
    private $clientId;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="clients")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="Acme\UserBundle\Entity\User", inversedBy="clients")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="client")
     */
    private $contacts;
    

    public function __construct() 
    {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set clientId
     *
     * @param string $clientId
     * @return Client
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    
        return $this;
    }

    /**
     * Get clientId
     *
     * @return string 
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    public function setCompany($company)
    {
        if ( empty($this->company) === FALSE ) {
            $this->company->removeClient($this);
        }

        $company->addClient($this);
        $this->company = $company;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setUser($user)
    {
        if ( empty($this->user) === FALSE ) {
            $this->user->removeClient($this);
        }

        $user->addClient($this);
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getContacts()
    {
        return $this->contacts;
    }

    public function addContact($contact)
    {
        if ( $this->contacts->contains($contact) === FALSE ) {
            $this->contacts[] = $contact;
        }
    }

    public function removeContact($contact)
    {
        if ( $this->contacts->contains($contact) === TRUE )
            $this->contacts->removeElement($contact);
    }
}
