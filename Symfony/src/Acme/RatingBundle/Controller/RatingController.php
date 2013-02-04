<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\RatingBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\Response;

class RatingController extends Controller
{
    public function indexAction()
    {
    }

    public function newAction()
    {
        $rateable = $this->getRateableFromRequest();

        $rating = new Rating();
        $rating->setStars($this->getStarsFromRequest());
        $rating->setRateable($rateable);

        if ( $this->isUserRater() === TRUE ) {
            $user = $this->getUserFromContext();
            $rating->setRatingUser($user);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rating);
        $entityManager->flush();
        
        $content = $this->renderView('AcmeRatingBundle:Rating:new.html.twig', array(
            'rating' => $rating,
            'rateable' => $rateable,
            'collection' => $rateable->getCollection(),
            'rateableImageURL' => $this->getImageURL($rateable),
        ));

        return new Response($content);
    }

    private function getStarsFromRequest()
    {
        $stars = intval($this->getRequest()->request->get('stars'));
        if ( ( $stars < 1 ) OR ( 5 < $stars ) )
            throw $this->createNotFoundException('Rating could not be determined.');

        return $stars;
    }

    private function getRateableFromRequest()
    {
        $rateableId = $this->getRequest()->request->get('rateableId');
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($rateableId);
        if ( empty($rateable) === TRUE )
            throw $this->createNotFoundException('The rateable does not exists.');

        return $rateable;
    }

    private function getUserFromContext()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) === TRUE )
            throw $this->createNotFoundException('Current user could not be found.');

        return $user;
    }

    private function isUserRater()
    {
        if ( $this->container->get('security.context')->isGranted('ROLE_RATER') === TRUE ) {
            return TRUE;
        }

        return FALSE;
    }

    private function getImageURL($rateable)
    {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( empty($image) === FALSE )
            $imageURL = $image->getWebPath();
        
        return $imageURL;
    }
}
