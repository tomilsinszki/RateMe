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
}
