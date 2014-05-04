<?php

namespace Acme\RatingBundle\EventListener;

use Acme\RatingBundle\Entity\Identifier;
use Acme\RatingBundle\Event\ModifyIdentifierEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Exception\ValidatorException;

class IdentifierModifier {

    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function modifyIdentifierForEntity(ModifyIdentifierEvent $event) {
        $identifier = $event->getNewIdentifier();
        $entity = $event->getEntity();

        $this->validateIdentifier($identifier);
        if (!$identifier) {
            $this->removeIdentifier($entity);
            return;
        }

        $this->validateIdentifierUniqueness($identifier, $entity);
        $idEntity = $this->getOrCreateIdentifier($entity);

        $this->populateIdentifier($idEntity, $identifier, $event->getQrCodeString());
    }

    private function validateIdentifier($identifier) {
        if (strlen($identifier) !== 4) {
            throw new ValidatorException('Az azonosító 4 karakter hosszú lehet csak!');
        }
    }

    private function removeIdentifier($entity) {
        if ($entity->getIdentifier()) {
            $this->em->remove($entity->getIdentifier());
            $rateableCollection->setIdentifier(null);
        }
    }

    private function validateIdentifierUniqueness($identifier, $entity) {
        $idEntity = $this->em->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($identifier);
        if ($idEntity && $entity->getIdentifier() !== $idEntity) {
            throw new ValidatorException('Ez az azonosító már foglalt!');
        }
    }

    private function getOrCreateIdentifier($entity) {
        if ($entity->getIdentifier()) {
            $identifier = $entity->getIdentifier();
        } else {
            $identifier = new Identifier();
            $entity->setIdentifier($identifier);
            $this->em->persist($identifier);
        }
        return $identifier;
    }

    private function populateIdentifier(Identifier $idEntity, $identifier, $qrStringPattern) {
        $idEntity->setAlphanumericValue($identifier);
        $idEntity->setQrCodeURL(str_replace('{{id}}', $identifier, $qrStringPattern));
        $idEntity->setUpdated(new \DateTime());
    }

} 