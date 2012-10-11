<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\RatingBundle\Entity\Identifier
 *
 * @ORM\Table(name="identifier")
 * @ORM\Entity
 */
class Identifier
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
     * @var string $qrCodeUrl
     *
     * @ORM\Column(name="qr_code_url", type="string", length=255, unique=true, nullable=false)
     */
    private $qrCodeUrl;

    /**
     * @var string $alphanumericValue
     *
     * @ORM\Column(name="alphanumeric_value", type="string", length=255, unique=true, nullable=false)
     */
    private $alphanumericValue;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;


    public function __construct() 
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * Set qrCodeUrl
     *
     * @param string $qrCodeUrl
     * @return Identifier
     */
    public function setQrCodeUrl($qrCodeUrl)
    {
        $this->qrCodeUrl = $qrCodeUrl;
    
        return $this;
    }

    /**
     * Get qrCodeUrl
     *
     * @return string 
     */
    public function getQrCodeUrl()
    {
        return $this->qrCodeUrl;
    }

    /**
     * Set alphanumericValue
     *
     * @param string $alphanumericValue
     * @return Identifier
     */
    public function setAlphanumericValue($alphanumericValue)
    {
        $this->alphanumericValue = $alphanumericValue;
    
        return $this;
    }

    /**
     * Get alphanumericValue
     *
     * @return string 
     */
    public function getAlphanumericValue()
    {
        return $this->alphanumericValue;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RateableCollection
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
     * @return RateableCollection
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
}
