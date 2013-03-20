<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity
 */
class Contact
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
     * @var datetime, $contactedAt
     *
     * @ORM\Column(name="contacted_at", type="datetime", nullable=false)
     */
    private $contactedAt;

    /**
     * @var datetime, $sentEmailAt
     *
     * @ORM\Column(name="sent_email_at", type="datetime", nullable=true)
     */
    private $sentEmailAt;

    /**
     * @var datetime, $clientVerifiedContactAt
     *
     * @ORM\Column(name="client_verified_contact_at", type="datetime", nullable=true)
     */
    private $clientVerifiedContactAt;

    /**
     * @var boolean $isValid
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="contacts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Rateable", inversedBy="contacts")
     * @ORM\JoinColumn(name="rateable_id", referencedColumnName="id")
     */
    private $rateable;

    /**
     * @ORM\OneToOne(targetEntity="Rating")
     * @ORM\JoinColumn(name="rating_id", referencedColumnName="id")
     */
    protected $rating;


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
     * Set contactedAt
     *
     * @param datetime, $contactedAt
     * @return Contact
     */
    public function setContactedAt(\datetime $contactedAt)
    {
        $this->contactedAt = $contactedAt;
    
        return $this;
    }

    /**
     * Get contactedAt
     *
     * @return datetime, 
     */
    public function getContactedAt()
    {
        return $this->contactedAt;
    }

    /**
     * Set sentEmailAt
     *
     * @param datetime, $sentEmailAt
     * @return Contact
     */
    public function setSentEmailAt(\datetime $sentEmailAt)
    {
        $this->sentEmailAt = $sentEmailAt;
    
        return $this;
    }

    /**
     * Get sentEmailAt
     *
     * @return datetime, 
     */
    public function getSentEmailAt()
    {
        return $this->sentEmailAt;
    }

    /**
     * Set clientVerifiedContactAt
     *
     * @param datetime, $clientVerifiedContactAt
     * @return Contact
     */
    public function setClientVerifiedContactAt(\datetime $clientVerifiedContactAt)
    {
        $this->clientVerifiedContactAt = $clientVerifiedContactAt;
    
        return $this;
    }

    /**
     * Get clientVerifiedContactAt
     *
     * @return datetime, 
     */
    public function getClientVerifiedContactAt()
    {
        return $this->clientVerifiedContactAt;
    }

    /**
     * Set isValid
     *
     * @param boolean $isValid
     * @return Contact
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
    
        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean 
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    public function setClient($client)
    {
        if ( empty($this->client) === FALSE ) {
            $this->client->removeContact($this);
        }

        $client->addContact($this);
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setRateable($rateable)
    {
        if ( empty($this->rateable) === FALSE ) {
            $this->rateable->removeContact($this);
        }

        $rateable->addContact($this);
        $this->rateable = $rateable;
    }

    public function getRateable()
    {
        return $this->rateable;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }
}
