<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\RatingBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Acme\RatingBundle\Utility\Validator;

class RatingController extends Controller
{

    public function addSuggestionForCompanyAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $response = false;
        
        $suggestionForCompany = $request->request->get('suggestionForCompany');
        $suggestionForCompany = substr($suggestionForCompany, 0, 500);

        if ( !empty($suggestionForCompany) ) {
            $directoryPath = realpath($this->get('kernel')->getRootDir()."/logs");
            $filePath = "$directoryPath/suggestionForCompany.csv";
            
            $bytesWrittenToFile = file_put_contents($filePath, "$suggestionForCompany\n-----\n", FILE_APPEND);
            if ( !empty($bytesWrittenToFile) ) {
                $response = true;
            }
        }
        
        return new Response(json_encode($response));
    }

    public function addEmailForSuggestionAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $response = false;
        
        $email = $request->request->get('email');
        if ( Validator::isEmailAddressValid($email) ) {
            $directoryPath = realpath($this->get('kernel')->getRootDir()."/logs");
            $filePath = "$directoryPath/emailForSuggestion.csv";
            
            $bytesWrittenToFile = file_put_contents($filePath, "$email\n-----\n", FILE_APPEND);
            if ( !empty($bytesWrittenToFile) ) {
                $response = true;
            }
        }
        
        return new Response(json_encode($response));
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

        if ( $this->wasLastNonContactRatingWithinAWeekForRateable($rateable) ) {
            return new Response($this->renderView('AcmeRatingBundle:Rating:alreadyRated.html.twig', array()));
        }

        $rating = new Rating();
        $rating->setStars($this->getStarsFromRequest());
        $rating->setRateable($rateable);

        if ( $this->isUserRater() === TRUE ) {
            $user = $this->getUserFromContext();
            $rating->setRatingUser($user);
        }

        $rating->setRatingIpAddress($this->getRequest()->getClientIp());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rating);
        $entityManager->flush();

        $contact               = $this->getDoctrine()->getRepository('AcmeRatingBundle:Contact')->findOneByRating($rating);
        $question              = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getNextQuestionForRating($rating);
        $maximumQuestionCount  = $rating->getRateable()->getCollection()->getMaxQuestionCount();
        $ratedQuestionsCount   = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getRatedQuestionsCountByRating($rating);
        $unratedQuestionsCount = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getUnratedQuestionsCountByRating($rating);

        if ( NULL != $maximumQuestionCount && ($unratedQuestionsCount + $ratedQuestionsCount) > $maximumQuestionCount ) {
            $unratedQuestionsCount = $maximumQuestionCount - $ratedQuestionsCount;
        }

        $html = $this->renderView('AcmeRatingBundle:Rating:new.html.twig', array(
            'rating'          => $rating,
            'rateable'        => $rateable,
            'question'        => $question,
            'questionsCount'  => $unratedQuestionsCount - 1,
            'contact'         => $contact,
            'profileImageURL' => $this->getImageURL($rateable),
        ));

        $response = new Response($html);

        $response->headers->setCookie(new Cookie(
            'noncontact_ratings',
            $this->getValueOfNonContactRatingsCookie($rateable),
            time() + (365 * 24 * 60 * 60)
        ));

        return $response;
    }

    private function wasLastNonContactRatingWithinAWeekForRateable($rateable)
    {
        $cookies = $this->getRequest()->cookies;

        if ( !$cookies->has('noncontact_ratings') ) {
            return false;
        }

        $nonContactRatingsByRateableId = json_decode($cookies->get('noncontact_ratings'), true);

        if ( !is_array($nonContactRatingsByRateableId) ) {
            return false;
        }
        
        if ( !array_key_exists($rateable->getId(), $nonContactRatingsByRateableId) ) {
            return false;
        }

        foreach($nonContactRatingsByRateableId[$rateable->getId()] as $nonContactRatingDateTimeString) {
            $currentDateTime = new \DateTime('now');
            $pastDatetime = \DateTime::createFromFormat('Y-m-d H:i:s', $nonContactRatingDateTimeString);
            $diff = $currentDateTime->getTimestamp() - $pastDatetime->getTimestamp();
            $diffInDays = (float)$diff/(float)(60.0*60.0*24.0);
            
            if ( $diffInDays < 7.0 ) {
                return true;
            }
        }

        return false;
    }

    private function getValueOfNonContactRatingsCookie($rateable)
    {
        $cookies = $this->getRequest()->cookies;
        if ( $cookies->has('noncontact_ratings') ) {
            $nonContactRatingsByRateableId = json_decode($cookies->get('noncontact_ratings'), true);
        }
        
        if ( !isset($nonContactRatingsByRateableId) || !is_array($nonContactRatingsByRateableId) ) {
            $nonContactRatingsByRateableId = array();
        }
        
        if ( !array_key_exists($rateable->getId(), $nonContactRatingsByRateableId) ) {
            $nonContactRatingsByRateableId[$rateable->getId()] = array();
        }
        
        $nonContactRatingsByRateableId[$rateable->getId()][] = date_format(date_create(), 'Y-m-d H:i:s');

        return json_encode($nonContactRatingsByRateableId);
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
        if ( empty($rateable) )
            throw $this->createNotFoundException('The rateable does not exists.');

        return $rateable;
    }

    private function getUserFromContext()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) )
            throw $this->createNotFoundException('Current user could not be found.');

        return $user;
    }

    private function isUserRater()
    {
        return $this->container->get('security.context')->isGranted('ROLE_RATER');
    }

    private function getImageURL($rateable)
    {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( !empty($image) )
            $imageURL = $image->getWebPath();
        
        return $imageURL;
    }
}
