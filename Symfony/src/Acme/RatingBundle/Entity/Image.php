<?php

namespace Acme\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Acme\RatingBundle\Entity\Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity
 */
class Image
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
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;


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
     * Set path
     *
     * @param string $path
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/images';
    }

    public function upload()
    {
        if (null === $this->file)
            return;

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        $this->path = $this->file->getClientOriginalName();
        $this->file = null;
    }
}
