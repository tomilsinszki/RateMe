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
     * @ORM\Column(name="email_address", type="string", length=255, nullable=false)
     */
    private $emailAddress;
    
    /**
     * @ORM\Column(name="first_name", type="string", length=255, unique=false, nullable=true)
     */
    private $firstName;
    
    /**
     * @ORM\Column(name="last_name", type="string", length=255, unique=false, nullable=false)
     */
    private $lastName;

    /**
     * @var datetime, $contactHappenedAt
     *
     * @ORM\Column(name="contact_happened_at", type="datetime", nullable=false)
     */
    private $contactHappenedAt;

    /**
     * @var datetime, $sentEmailAt
     *
     * @ORM\Column(name="sent_email_at", type="datetime", nullable=true)
     */
    private $sentEmailAt;

    /**
     * @var datetime, $clientFlaggedEmailAsFlawedAt
     *
     * @ORM\Column(name="client_flagged_email_as_flawed_at", type="datetime", nullable=true)
     */
    private $clientFlaggedEmailAsFlawedAt;

    /**
     * @ORM\ManyToOne(targetEntity="VerifiedClient", inversedBy="contacts")
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
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return Contact
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    
        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Contact
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Contact
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set contactHappenedAt
     *
     * @param datetime, $contactHappenedAt
     * @return Contact
     */
    public function setContactHappenedAt(\datetime $contactHappenedAt)
    {
        $this->contactHappenedAt = $contactHappenedAt;
    
        return $this;
    }

    /**
     * Get contactHappenedAt
     *
     * @return datetime, 
     */
    public function getContactHappenedAt()
    {
        return $this->contactHappenedAt;
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
     * Set clientFlaggedEmailAsFlawedAt
     *
     * @param datetime, $clientFlaggedEmailAsFlawedAt
     * @return Contact
     */
    public function setClientFlaggedEmailAsFlawedAt(\datetime $clientFlaggedEmailAsFlawedAt)
    {
        $this->clientFlaggedEmailAsFlawedAt = $clientFlaggedEmailAsFlawedAt;
    
        return $this;
    }

    /**
     * Get clientFlaggedEmailAsFlawedAt
     *
     * @return datetime, 
     */
    public function getClientFlaggedEmailAsFlawedAt()
    {
        return $this->clientFlaggedEmailAsFlawedAt;
    }

    public function setVerifiedClient($client)
    {
        if ( empty($this->client) === FALSE ) {
            $this->client->removeContact($this);
        }

        $client->addContact($this);
        $this->client = $client;
    }

    public function getVerifiedClient()
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
