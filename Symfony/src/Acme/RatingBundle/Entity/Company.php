<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=511)
     */
    private $name;

    /**
     * @var string $emailBodySchema
     *
     * @ORM\Column(name="emailBodySchema", type="text", nullable=true)
     */
    private $emailBodySchema;

    /**
     * @ORM\OneToMany(targetEntity="RateableCollection", mappedBy="company")
     */
    private $rateableCollections;

    /**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="company")
     */
    private $clients;
    

    public function __construct() 
    {
        $this->rateableCollections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Company
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
     * Set emailBodySchema
     *
     * @param string $emailBodySchema
     * @return Company
     */
    public function setEmailBodySchema($emailBodySchema)
    {
        $this->emailBodySchema = $emailBodySchema;
    
        return $this;
    }

    /**
     * Get emailBodySchema
     *
     * @return string 
     */
    public function getEmailBodySchema()
    {
        return $this->emailBodySchema;
    }

    public function getRateableCollections()
    {
        return $this->rateableCollections;
    }

    public function addRateableCollection($rateableCollection)
    {
        if ( $this->rateableCollections->contains($rateableCollection) === FALSE ) {
            $this->rateableCollections[] = $rateableCollection;
        }
    }

    public function removeRateableCollection($rateableCollection)
    {
        if ( $this->rateableCollections->contains($rateableCollection) === TRUE )
            $this->rateableCollections->removeElement($rateableCollection);
    }

    public function getClients()
    {
        return $this->clients;
    }

    public function addClient($client)
    {
        if ( $this->clients->contains($client) === FALSE ) {
            $this->clients[] = $client;
        }
    }

    public function removeClient($client)
    {
        if ( $this->clients->contains($client) === TRUE )
            $this->clients->removeElement($client);
    }
}
