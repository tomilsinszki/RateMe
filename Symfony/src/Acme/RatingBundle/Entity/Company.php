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
     * @var string $ratingPageBackgroundColor
     *
     * @ORM\Column(name="ratingPageBackgroundColor", type="text", nullable=false)
     */
    private $ratingPageBackgroundColor;

    /**
     * @var string $ratingPageFontColor
     *
     * @ORM\Column(name="ratingPageFontColor", type="text", nullable=false)
     */
    private $ratingPageFontColor;

    /**
     * @var string $ratingPageStarsSubtitleFontColor
     *
     * @ORM\Column(name="ratingPageStarsSubtitleFontColor", type="text", nullable=false)
     */
    private $ratingPageStarsSubtitleFontColor;

    /**
     * @var string $ratingPageCancelSubratingFontColor
     *
     * @ORM\Column(name="ratingPageCancelSubratingFontColor", type="text", nullable=false)
     */
    private $ratingPageCancelSubratingFontColor;

    /**
     * @var string $ratingPromotionPrizeName
     *
     * @ORM\Column(name="ratingPromotionPrizeName", type="text", nullable=true)
     */
    private $ratingPromotionPrizeName;

    /**
     * @var string $ratingPromotionRulesURL
     *
     * @ORM\Column(name="ratingPromotionRulesURL", type="text", nullable=true)
     */
    private $ratingPromotionRulesURL;

    /**
     * @ORM\OneToMany(targetEntity="RateableCollection", mappedBy="company")
     */
    private $rateableCollections;

    /**
     * @ORM\OneToMany(targetEntity="VerifiedClient", mappedBy="company")
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

    public function getRatingPageBackgroundColor()
    {
        return $this->ratingPageBackgroundColor;
    }

    public function setRatingPageBackgroundColor($ratingPageBackgroundColor)
    {
        $this->ratingPageBackgroundColor = $ratingPageBackgroundColor;
        return $this;
    }

    public function getRatingPageFontColor()
    {
        return $this->ratingPageFontColor;
    }

    public function setRatingPageFontColor($ratingPageFontColor)
    {
        $this->ratingPageFontColor = $ratingPageFontColor;
        return $this;
    }

    public function getRatingPageStarsSubtitleFontColor()
    {
        return $this->ratingPageStarsSubtitleFontColor;
    }

    public function setRatingPageStarsSubtitleFontColor($ratingPageStarsSubtitleFontColor)
    {
        $this->ratingPageStarsSubtitleFontColor = $ratingPageStarsSubtitleFontColor;
        return $this;
    }

    public function getRatingPageCancelSubratingFontColor()
    {
        return $this->ratingPageCancelSubratingFontColor;
    }

    public function setRatingPageCancelSubratingFontColor($ratingPageCancelSubratingFontColor)
    {
        $this->ratingPageCancelSubratingFontColor = $ratingPageCancelSubratingFontColor;
        return $this;
    }

    public function getRatingPromotionPrizeName()
    {
        return $this->ratingPromotionPrizeName;
    }

    public function setRatingPromotionPrizeName($ratingPromotionPrizeName)
    {
        $this->ratingPromotionPrizeName = $ratingPromotionPrizeName;
        return $this;
    }

    public function getRatingPromotionRulesURL()
    {
        return $this->ratingPromotionRulesURL;
    }

    public function setRatingPromotionRulesURL($ratingPromotionRulesURL) 
    {
        $this->ratingPromotionRulesURL = $ratingPromotionRulesURL;
        return $this;
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

    public function getVerifiedClients()
    {
        return $this->clients;
    }

    public function addVerifiedClient($client)
    {
        if ( $this->clients->contains($client) === FALSE ) {
            $this->clients[] = $client;
        }
    }

    public function removeVerifiedClient($client)
    {
        if ( $this->clients->contains($client) === TRUE )
            $this->clients->removeElement($client);
    }
}
