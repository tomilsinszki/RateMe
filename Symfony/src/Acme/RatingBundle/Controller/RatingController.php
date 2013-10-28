<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\RatingBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Acme\RatingBundle\Utility\Validator;

class RatingController extends Controller
{
    public function indexAction()
    {
    }

    public function setEmailAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }

        $rating = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:Rating')->find(intval($request->request->get('ratingId')));
        if ( empty($rating) ) {
            throw $this->createNotFoundException('No rating found for ID.');
            return null;
        }

        $email = $request->request->get('email');
        if ( Validator::isEmailAddressValid($email) ) {
            $rating->setEmail($email);
            $rating->setUpdated(new \DateTime());
            $this->getDoctrine()->getManager()->persist($rating);
            $this->getDoctrine()->getManager()->flush();
        }

        return new Response('');
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

        return $this->render('AcmeRatingBundle:Rating:new.html.twig', array(
            'rating' => $rating,
            'rateable' => $rateable,
            'question' => $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getNextQuestionForRating($rating),
            'contact' => $this->getDoctrine()->getRepository('AcmeRatingBundle:Contact')->findOneByRating($rating),
            'profileImageURL' => $this->getImageURL($rateable),
        ));
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
