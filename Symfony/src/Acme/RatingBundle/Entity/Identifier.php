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
     * @var string $qr_code_url
     *
     * @ORM\Column(name="qr_code_url", type="string", length=255, unique=true, nullable=false)
     */
    private $qr_code_url;

    /**
     * @var string $alphanumeric_value
     *
     * @ORM\Column(name="alphanumeric_value", type="string", length=255, unique=true, nullable=false)
     */
    private $alphanumeric_value;


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
     * Set qr_code_url
     *
     * @param string $qrCodeUrl
     * @return Identifier
     */
    public function setQrCodeUrl($qrCodeUrl)
    {
        $this->qr_code_url = $qrCodeUrl;
    
        return $this;
    }

    /**
     * Get qr_code_url
     *
     * @return string 
     */
    public function getQrCodeUrl()
    {
        return $this->qr_code_url;
    }

    /**
     * Set alphanumeric_value
     *
     * @param string $alphanumericValue
     * @return Identifier
     */
    public function setAlphanumericValue($alphanumericValue)
    {
        $this->alphanumeric_value = $alphanumericValue;
    
        return $this;
    }

    /**
     * Get alphanumeric_value
     *
     * @return string 
     */
    public function getAlphanumericValue()
    {
        return $this->alphanumeric_value;
    }
}
