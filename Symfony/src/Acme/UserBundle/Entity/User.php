<?php

namespace Acme\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Acme\UserBundle\Entity\User
 *
 * @ORM\Table(name="acme_users")
 * @ORM\Entity(repositoryClass="Acme\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = TRUE;
        $this->salt = md5(uniqid(null, TRUE));
    }

    public function getUsername() { return $this->username; }
    public function getSalt() { return $this->salt; }
    public function getPassword() { return $this->password; }
    public function getEmail() { return $this->email; }
    public function isActive() { return $this->isActive; }

    public function setUsername($username) { $this->username = $username; }
    public function setSalt($salt) { $this->salt = $salt; }
    public function setPassword($password) { $this->password = $password; }
    public function setEmail($email) { $this->email = $email; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getRoles() { return array('ROLE_USER'); }

    public function eraseCredentials()
    {
    }
}
