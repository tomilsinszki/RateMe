<?php

namespace Acme\RatingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class ModifyIdentifierEvent extends Event {

    protected $entity;

    protected $newIdentifier;

    function __construct($entity, $newIdentifier)
    {
        $this->entity = $entity;
        $this->newIdentifier = $newIdentifier;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getNewIdentifier()
    {
        return $this->newIdentifier;
    }

    public abstract function getQrCodeString();

}