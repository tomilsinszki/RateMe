<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Acme\RatingBundle\Entity\Rating;
use Acme\RatingBundle\Utility\Validator;

class ContactController extends Controller
{
    private $rateableForUser = null;
    private $companyForUser = null;
    private $companyIdForUser = null;

    private $autocompleteByEmailPrefixQueryText = 
        '( 
            SELECT
                co.email_address AS emailAddress,
                co.first_name AS firstName,
                co.last_name AS lastName,
                NULL AS clientId,
                co.contact_happened_at AS contactHappenedAt
            FROM contact co
            WHERE 
                co.email_address LIKE "%1$s%%" 
        )
        
        UNION DISTINCT
        
        ( 
            SELECT
                cl.email_address AS emailAddress,
                cl.first_name AS firstName,
                cl.last_name AS lastName,
                cl.client_id AS clientId,
                NULL AS contactHappenedAt
            FROM verified_client cl
            WHERE 
                cl.email_address LIKE "%1$s%%"
                AND cl.company_id=%2$d
        )
            
        ORDER BY clientId DESC, contactHappenedAt DESC';
    
    private $autocompleteByEmailPrefixStatement = null;
    private $autocompleteForEmails = array();
    private $autocompleteDataByEmail = array();

    private $autocompleteByClientIdQueryText = 
        'SELECT
            first_name AS firstName,
            last_name AS lastName,
            client_id AS clientId,
            email_address AS emailAddress
        FROM verified_client
        WHERE 
            client_id="%1$s"
            AND company_id=%2$d';
    
    private $autocompleteByClientIdStatement = null;
    private $autocompleteForClientIds = array();
    private $autocompleteDataByClientId = array();

    private $contactFormData = array();
    private $verifiedClientWithClientId = null;
    private $verifiedClientWithEmail = null;
    private $lastInsertedOrUpdatedVerifiedClientId = null;

    public function indexAction(Request $request)
    {
        if ( $this->isCurrentUserAllowedToDoQuiz() ) {
            return $this->redirect($this->generateUrl('quiz_entrance'));
        }

        $defaultData = array();
        $contactForm = $this->createFormBuilder($defaultData)
            ->add('email', 'email', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('clientId', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->add('lastName', 'text', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->getForm();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);

        return $this->render('AcmeRatingBundle:Contact:index.html.twig', array(
            'form' => $contactForm->createView(),
        ));
    }
    
    private function isCurrentUserAllowedToDoQuiz() {
        $user = $this->get('security.context')->getToken()->getUser();
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
        $questions = $this->getDoctrine()->getRepository('AcmeQuizBundle:Question')->find3RandomQuestionsNotShownInTheLast2Weeks($rateable);
        
        if ( 3 <= count($questions) ) {
            $completedQuiz = $this->getDoctrine()->getRepository('AcmeQuizBundle:Quiz')->createQueryBuilder('q')
                ->where('q.rateable = :rateable')
                ->setParameter('rateable', $rateable)
                ->orderBy('q.created', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            
            if (!$completedQuiz || $this->isDateOlderThan8Hours($completedQuiz->getCreated())) {
                return true;
            }
        }
        
        return false;
    }

    private function isDateOlderThan8Hours(\DateTime $date) {
        return $date->getTimestamp() + 8*60*60 < time();
    }
    
    public function newAction(Request $request)
    {
        $defaultData = array();
        $contactForm = $this->createFormBuilder($defaultData)
            ->add('email', 'email', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('clientId', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->add('lastName', 'text', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->getForm();

        $this->loadRateableForCurrentUser();
        
        if ( $request->isMethod('POST') ) {
            $contactForm->bind($request);
            $contactFormData = $contactForm->getData();
            $isContactFormValid = TRUE;

            if ( empty($contactFormData['email']) === TRUE ) {
                $contactForm->get('email')->addError(new FormError('Kötelező e-mail címet megadni!'));
                $isContactFormValid = FALSE;
            }

            if ( Validator::isEmailAddressValid($contactFormData['email']) === FALSE ) {
                $contactForm->get('email')->addError(new FormError('Az e-mail cím formátuma hibás!'));
                $isContactFormValid = FALSE;
            }
            
            if ( empty($contactFormData['lastName']) === TRUE ) {
                $contactForm->get('lastName')->addError(new FormError('Kötelező vezetéknevet megadni!'));
                $isContactFormValid = FALSE;
            }

            if ( $isContactFormValid === TRUE ) {
                $contactFormData['lastName'] = mb_convert_case($contactFormData['lastName'], MB_CASE_TITLE, "UTF-8");
                $contactFormData['firstName'] = mb_convert_case($contactFormData['firstName'], MB_CASE_TITLE, "UTF-8");
                $this->contactFormData = $contactFormData;
                $this->saveContactForm();
                return $this->redirect($this->generateUrl('contact_index'));
            }
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
        $questions = $this->getDoctrine()->getRepository('AcmeQuizBundle:Question')->find3RandomQuestionsNotShownInTheLast2Weeks($rateable);
        
        return $this->render('AcmeRatingBundle:Contact:index.html.twig', array(
            'form' => $contactForm->createView(),
            'quizQuestions' => $questions,
        ));
    }

    private function loadRateableForCurrentUser() {
        $user = $this->get('security.context')->getToken()->getUser();
        $this->rateableForUser = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
    }

    private function saveContactForm() {
        if ( $this->shouldBeSavedAsVerifiedClient() === TRUE ) {
            $this->saveAsVerifiedClient();
        }
        else if ( $this->shouldBeSevedAsContact() === TRUE ) {
            $this->saveAsContact();
        }
    }

    private function shouldBeSavedAsVerifiedClient() {
        $isEmailSet = ( empty($this->contactFormData['email']) === FALSE );
        $isClientIdSet = ( empty($this->contactFormData['clientId']) === FALSE );
        $isLastNameSet = ( empty($this->contactFormData['lastName']) === FALSE );

        return ( $isEmailSet AND $isClientIdSet AND $isLastNameSet );
    }

    private function shouldBeSevedAsContact() {
        $isEmailSet = ( empty($this->contactFormData['email']) === FALSE );
        $isClientIdNotSet = ( empty($this->contactFormData['clientId']) === TRUE );
        $isLastNameSet = ( empty($this->contactFormData['lastName']) === FALSE );

        return ( $isEmailSet AND $isClientIdNotSet AND $isLastNameSet );
    }

    private function saveAsVerifiedClient() {
        $this->loadCompanyAndCompanyIdForUser();

        $this->verifiedClientWithClientId = $this->getDoctrine()->getRepository('AcmeRatingBundle:VerifiedClient')->findOneBy(array(
            'company' => $this->companyForUser,
            'clientId' => $this->contactFormData['clientId'],
        ));

        $this->verifiedClientWithEmail = $this->getDoctrine()->getRepository('AcmeRatingBundle:VerifiedClient')->findOneBy(array(
            'company' => $this->companyForUser,
            'emailAddress' => $this->contactFormData['email'],
        ));

        $clientIdExists = ( empty($this->verifiedClientWithClientId) === FALSE );
        $emailExists = ( empty($this->verifiedClientWithEmail) === FALSE );

        if ( ( $clientIdExists === TRUE ) AND ( $emailExists === TRUE ) ) {
            if ( $this->verifiedClientWithClientId->getId() === $this->verifiedClientWithEmail->getId() ) {
                $this->saveAsVerifiedClientIfBothClientIdAndEmailExistAndMatch();
            }
            else {
                $this->saveAsVerifiedClientIfBothClientIdAndEmailExistAndMismatch();
            }
        }
        else if ( ( $clientIdExists === TRUE ) AND ( $emailExists === FALSE ) ) {
            $this->saveAsVerifiedClientIfOnlyClientIdExists();
        }
        else if ( ( $clientIdExists === FALSE ) AND ( $emailExists === TRUE ) ) {
            $this->saveAsVerifiedClientIfOnlyEmailExists();
        }
        else if ( ( $clientIdExists === FALSE ) AND ( $emailExists === FALSE ) ) {
            $this->saveAsVerifiedClientIfNeitherClientIdOrEmailExists();
        }

        $this->createContactForVerifiedClient();
    }

    private function loadCompanyAndCompanyIdForUser() {
        $this->loadRateableForCurrentUser();

        $rateableCollectionForUser = $this->rateableForUser->getCollection();
        if ( empty($rateableCollectionForUser) === TRUE ) {
            throw $this->createNotFoundException('Rateable collection not found for current user!');
        }

        $this->companyForUser = $rateableCollectionForUser->getCompany();
        if ( empty($this->companyForUser) === TRUE ) {
            throw $this->createNotFoundException('Company not found for current user!');
        }

        $this->companyIdForUser = $this->companyForUser->getId();

        return $this->companyIdForUser;
    }
    
    private function saveAsVerifiedClientIfBothClientIdAndEmailExistAndMatch() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();

        $connection->update(
            'verified_client', 
            array(
                'company_id' => $this->loadCompanyAndCompanyIdForUser(),
                'client_id' => $this->contactFormData['clientId'],
                'first_name' => $this->contactFormData['firstName'],
                'last_name' => $this->contactFormData['lastName'],
                'email_address' => $this->contactFormData['email'],
            ),
            array(
                'id' => $this->verifiedClientWithClientId->getId()
            )
        );

        $this->lastInsertedOrUpdatedVerifiedClientId = $this->verifiedClientWithClientId->getId();
    }

    private function saveAsVerifiedClientIfBothClientIdAndEmailExistAndMismatch() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();

        $connection->delete('verified_client', array('id' => $this->verifiedClientWithClientId->getId()));
        $connection->delete('verified_client', array('id' => $this->verifiedClientWithEmail->getId()));

        $connection->insert('verified_client', array(
            'company_id' => $this->loadCompanyAndCompanyIdForUser(),
            'client_id' => $this->contactFormData['clientId'],
            'first_name' => $this->contactFormData['firstName'],
            'last_name' => $this->contactFormData['lastName'],
            'email_address' => $this->contactFormData['email'],
        ));

        $this->lastInsertedOrUpdatedVerifiedClientId = $connection->lastInsertId();
    }

    private function saveAsVerifiedClientIfOnlyClientIdExists() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();

        $connection->update(
            'verified_client', 
            array(
                'company_id' => $this->loadCompanyAndCompanyIdForUser(),
                'client_id' => $this->contactFormData['clientId'],
                'first_name' => $this->contactFormData['firstName'],
                'last_name' => $this->contactFormData['lastName'],
                'email_address' => $this->contactFormData['email'],
            ),
            array(
                'id' => $this->verifiedClientWithClientId->getId()
            )
        );

        $this->lastInsertedOrUpdatedVerifiedClientId = $this->verifiedClientWithClientId->getId();
    }

    private function saveAsVerifiedClientIfOnlyEmailExists() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();

        $connection->update(
            'verified_client', 
            array(
                'company_id' => $this->loadCompanyAndCompanyIdForUser(),
                'client_id' => $this->contactFormData['clientId'],
                'first_name' => $this->contactFormData['firstName'],
                'last_name' => $this->contactFormData['lastName'],
                'email_address' => $this->contactFormData['email'],
            ),
            array(
                'id' => $this->verifiedClientWithEmail->getId()
            )
        );

        $this->lastInsertedOrUpdatedVerifiedClientId = $this->verifiedClientWithEmail->getId();
    }

    private function saveAsVerifiedClientIfNeitherClientIdOrEmailExists() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        
        $connection->insert('verified_client', array(
            'company_id' => $this->loadCompanyAndCompanyIdForUser(),
            'client_id' => $this->contactFormData['clientId'],
            'first_name' => $this->contactFormData['firstName'],
            'last_name' => $this->contactFormData['lastName'],
            'email_address' => $this->contactFormData['email'],
        ));

        $this->lastInsertedOrUpdatedVerifiedClientId = $connection->lastInsertId();
    }

    private function createContactForVerifiedClient() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        
        $connection->insert('contact', array(
            'first_name' => $this->contactFormData['firstName'],
            'last_name' => $this->contactFormData['lastName'],
            'email_address' => $this->contactFormData['email'],
            'contact_happened_at' => date('Y-m-d H:i:s'),
            'client_id' => $this->lastInsertedOrUpdatedVerifiedClientId,
            'rateable_id' => $this->rateableForUser->getId(),
        ));
    }

    private function saveAsContact() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        
        $connection->insert('contact', array(
            'first_name' => $this->contactFormData['firstName'],
            'last_name' => $this->contactFormData['lastName'],
            'email_address' => $this->contactFormData['email'],
            'contact_happened_at' => date('Y-m-d H:i:s'),
            'rateable_id' => $this->rateableForUser->getId(),
        ));
    }

    public function autocompleteByEmailPrefixAction(Request $request)
    {
        $this->autocompleteForEmails = array();
        $this->autocompleteDataByEmail = array();
        $this->loadAutocompleteByEmailPrefixStatement($request->request->get('emailPrefix'));
        
        foreach($this->autocompleteByEmailPrefixStatement->fetchAll() AS $row) {
            if ( empty($row['clientId']) == FALSE ) {
                $this->addClientDataToAutocompleteForEmails($row);
            }
            else {
                $this->addContactDataToAutocompleteForEmails($row);
            }
        }

        $returnData = array(
            'emails' => $this->autocompleteForEmails,
            'dataByEmail' => $this->autocompleteDataByEmail,
        );
        
        return new Response(json_encode($returnData), 200, array('Content-Type' => 'application/json'));
    }

    private function loadAutocompleteByEmailPrefixStatement($emailPrefix) {
        $connection = $this->get('database_connection');
        $queryText = sprintf($this->autocompleteByEmailPrefixQueryText, $emailPrefix, $this->loadCompanyAndCompanyIdForUser());
        $this->autocompleteByEmailPrefixStatement = $connection->executeQuery($queryText);
        $this->autocompleteByEmailPrefixStatement->execute();
    }

    private function addClientDataToAutocompleteForEmails($row) {
        $email = strtolower($row['emailAddress']);

        $this->autocompleteDataByEmail[$email] = array(
            'clientId' => $row['clientId'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
        );

        $this->addToAutocompleteForEmails($email);
    }

    private function addContactDataToAutocompleteForEmails($row) {
        $email = strtolower($row['emailAddress']);

        if ( empty($this->autocompleteDataByEmail[$email]) == FALSE ) {
            return;
        }
        
        $this->autocompleteDataByEmail[$email] = array(
            'clientId' => '',
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
        );

        $this->addToAutocompleteForEmails($email);
    }

    private function addToAutocompleteForEmails($email) {
        $email = strtolower($email);

        if ( in_array($email, $this->autocompleteForEmails) === FALSE ) {
            array_push($this->autocompleteForEmails, $email);
        }
    }

    public function autocompleteByClientIdAction(Request $request)
    {
        $this->autocompleteForClientId = array();
        $this->autocompleteDataByClientId = array();
        $this->loadAutocompleteByClientIdStatement($request->request->get('clientId'));
        
        foreach($this->autocompleteByClientIdStatement->fetchAll() AS $row) {
            $this->addDataToAutocompleteForClientId($row);
        }

        $returnData = array(
            'clientIds' => $this->autocompleteForClientIds,
            'dataByClientId' => $this->autocompleteDataByClientId,
        );
        
        return new Response(json_encode($returnData), 200, array('Content-Type' => 'application/json'));
    }

    private function loadAutocompleteByClientIdStatement($clientId) {
        $connection = $this->get('database_connection');
        $queryText = sprintf($this->autocompleteByClientIdQueryText, $clientId, $this->loadCompanyAndCompanyIdForUser());
        $this->autocompleteByClientIdStatement = $connection->executeQuery($queryText);
        $this->autocompleteByClientIdStatement->execute();
    }
    
    private function addDataToAutocompleteForClientId($row) {
        $clientId = $row['clientId'];
            
        $this->autocompleteDataByClientId[$clientId] = array(
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'emailAddress' => strtolower($row['emailAddress']),
        );

        $this->addToAutocompleteForClientId($clientId);
    }

    private function addToAutocompleteForClientId($clientId) {
        array_push($this->autocompleteForClientIds, $clientId);
    }

    public function voteAction($token, $stars)
    {
        $stars = intval($stars);

        if ( ( $stars < 1 ) OR ( 5 < $stars ) ) {
            throw $this->createNotFoundException('Invalid rating!');
        }
        
        $contact = $this->getDoctrine()->getRepository('AcmeRatingBundle:Contact')->findOneByRateToken($token);
        if ( empty($contact) === TRUE ) {
            throw $this->createNotFoundException('Invalid rating!');
        }
        
        $isEmailNonExpired = ( $this->daysPassedSince($contact->getSentEmailAt()) < 3.0 );
        
        $rating = $contact->getRating();
        if ( ( empty($rating) === TRUE ) AND ( $isEmailNonExpired === TRUE ) ) {
            $this->createRatingForContact($contact, $stars);
        }
        else if( ($this->minsPassedSince($rating->getCreated()) < 10.0) AND ( $isEmailNonExpired === TRUE ) ) {
            $this->updateRating($rating, $stars);
        }
        else {
            $stars = $contact->getRating()->getStars();
        }
        
        if ( empty($stars) === TRUE ) {
            throw $this->createNotFoundException('Email has expired, no rating was given!');
        }

        $question              = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getNextQuestionForRating($contact->getRating());
        $maximumQuestionCount  = $contact->getRating()->getRateable()->getCollection()->getMaxQuestionCount();
        $ratedQuestionsCount   = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getRatedQuestionsCountByRating($contact->getRating());        
        $unratedQuestionsCount = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getUnratedQuestionsCountByRating($contact->getRating());                
        
        if(NULL != $maximumQuestionCount && ($unratedQuestionsCount + $ratedQuestionsCount) > $maximumQuestionCount) {            
            $unratedQuestionsCount = $maximumQuestionCount - $ratedQuestionsCount;
        }        
        
        return $this->render('AcmeRatingBundle:Rating:new.html.twig', array(
            'rating' => $contact->getRating(),
            'contact' => $contact,
            'rateable' => $contact->getRateable(),
            'question' => $question,
            'questionsCount' => $unratedQuestionsCount - 1,
            'profileImageURL' => $this->getImageURL($contact->getRateable()),
            'company' => $contact->getRateable()->getCollection()->getCompany(),
        ));
    }
    
    private static function daysPassedSince($pastDatetime) {
        $currentDateTime = new \DateTime('now');
        $diff = $currentDateTime->getTimestamp() - $pastDatetime->getTimestamp();
        $diffInDays = (float)$diff/(float)(60.0*60.0*24.0);
        return $diffInDays;
    }
    
    private function createRatingForContact($contact, $stars) {
        $entityManager = $this->getDoctrine()->getManager();
        $rating = new Rating();
        $rating->setRateable($contact->getRateable());
        $rating->setStars($stars);
        $rating->setRatingIpAddress($this->getRequest()->getClientIp());
        $rating->setCreated(new \DateTime());
        $rating->setUpdated(new \DateTime());
        $contact->setRating($rating);
        $entityManager->persist($rating);
        $entityManager->persist($contact);
        $entityManager->flush();
    }
    
    private static function minsPassedSince($pastDatetime) {
        $currentDateTime = new \DateTime('now');
        $diff = $currentDateTime->getTimestamp() - $pastDatetime->getTimestamp();
        $diffInMins = (float)$diff/(float)60.0;
        return $diffInMins;
    }

    private function updateRating($rating, $stars) {
        $entityManager = $this->getDoctrine()->getManager();
        $rating->setStars($stars);
        $rating->setUpdated(new \DateTime());
        $entityManager->persist($rating);
        $entityManager->flush();
    }

    private function getImageURL($rateable) {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( empty($image) === FALSE )
            $imageURL = $image->getWebPath();
        
        return $imageURL;
    }

    public function flagEmailAsFlawedAction($token) {
        $contact = $this->getDoctrine()->getRepository('AcmeRatingBundle:Contact')->findOneByRateToken($token);
        if ( empty($contact) === TRUE ) {
            throw $this->createNotFoundException('Invalid token!');
        }
        
        $isEmailNonExpired = ( $this->daysPassedSince($contact->getSentEmailAt()) < 3.0 );

        $rating = $contact->getRating();
        if ( empty($rating) === FALSE ) {
            if ( 10.0 <= $this->minsPassedSince($rating->getCreated()) ) {
                $isEmailNonExpired = FALSE;
            }
        }

        if ( $isEmailNonExpired === TRUE ) {
            $entityManager = $this->getDoctrine()->getManager();
            $contact->setClientFlaggedEmailAsFlawedAt(new \DateTime());
            $entityManager->persist($contact);
            $entityManager->flush();
        }

        return new Response('Visszajelzését rögzítettük. Köszönjük!');
    }
}
