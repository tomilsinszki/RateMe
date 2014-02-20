<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Acme\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    protected $password;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @ORM\Column(name="first_name", type="string", length=255, unique=false, nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255, unique=false, nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\OneToOne(targetEntity="Acme\RatingBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

    /**
     * @ORM\ManyToMany(targetEntity="Acme\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="Acme\RatingBundle\Entity\RateableCollection", mappedBy="owners")
     */
    protected $ownedCollections;

    /**
     * @ORM\OneToMany(targetEntity="Acme\RatingBundle\Entity\VerifiedClient", mappedBy="user")
     */
    private $clients;
    
    /**
     * @ORM\Column(name="email_address", type="string", length=255, unique=true, nullable=true)
     */
    private $emailAddress;

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->salt,
            $this->password,
            $this->username,
            $this->isActive,
            $this->groups
            //$this->ownedCollections
            //$this->clients
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->salt,
            $this->password,
            $this->username,
            $this->isActive,
            $this->groups
            //$this->ownedCollections
            //$this->clients
        ) = unserialize($serialized);
    }

    public function __construct()
    {
        $this->isActive = TRUE;
        $this->salt = md5(uniqid(null, TRUE));
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ownedCollections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getUsername() { return $this->username; }
    public function getSalt() { return $this->salt; }
    public function getPassword() { return $this->password; }
    public function isActive() { return $this->isActive; }
    public function getOwnedCollections() { return $this->ownedCollections; }

    public function setUsername($username) { $this->username = $username; }
    public function setSalt($salt) { $this->salt = $salt; }
    public function setPassword($password) { $this->password = $password; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function addGroup($group)
    {
        if ( $this->groups->contains($group) === FALSE )
            $this->groups[] = $group;
    }

    public function removeGroup($group)
    {
        if ( $this->groups->contains($group) === TRUE )
            $this->groups->removeElement($group);
    }
    
    public function getRoles()
    {
        return $this->groups->toArray();
    }

    public function eraseCredentials()
    {
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

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
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
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
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
     * @return User
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
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return User
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
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add ownedCollections
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection $ownedCollections
     * @return User
     */
    public function addOwnedCollection(\Acme\RatingBundle\Entity\RateableCollection $ownedCollections)
    {
        $this->ownedCollections[] = $ownedCollections;
    
        return $this;
    }

    /**
     * Remove ownedCollections
     *
     * @param \Acme\RatingBundle\Entity\RateableCollection $ownedCollections
     */
    public function removeOwnedCollection(\Acme\RatingBundle\Entity\RateableCollection $ownedCollections)
    {
        $this->ownedCollections->removeElement($ownedCollections);
    }

    /**
     * Add clients
     *
     * @param \Acme\RatingBundle\Entity\VerifiedClient $clients
     * @return User
     */
    public function addClient(\Acme\RatingBundle\Entity\VerifiedClient $clients)
    {
        $this->clients[] = $clients;
    
        return $this;
    }

    /**
     * Remove clients
     *
     * @param \Acme\RatingBundle\Entity\VerifiedClient $clients
     */
    public function removeClient(\Acme\RatingBundle\Entity\VerifiedClient $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }
}