<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Acme\RatingBundle\Entity\Rating;

class ContactController extends Controller
{
    private $rateableForUser = null;

    private $autocompleteByEmailPrefixQueryText = 
        'SELECT
            co.email_address AS contactEmailAddress,
            co.first_name AS contactFirstName,
            co.last_name AS contactLastName,
            cl.client_id AS clientClientId,
            cl.email_address AS clientEmailAddress,
            cl.first_name AS clientFirstName,
            cl.last_name AS clientLastName
        FROM contact co 
        LEFT JOIN verified_client cl ON co.client_id=cl.id 
        WHERE 
            co.email_address LIKE "%1$s%%"
            OR cl.email_address LIKE "%1$s%%"
        ORDER BY contact_happened_at DESC';
    
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
            client_id="%1$s"';
    
    private $autocompleteByClientIdStatement = null;
    private $autocompleteForClientIds = array();
    private $autocompleteDataByClientId = array();

    private $contactFormData = array();
    private $verifiedClientWithClientId = null;
    private $verifiedClientWithEmail = null;
    private $lastInsertedOrUpdatedVerifiedClientId = null;

    public function indexAction(Request $request)
    {
        $defaultData = array();
        $contactForm = $this->createFormBuilder($defaultData)
            ->add('email', 'email', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('clientId', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->add('lastName', 'text', array('required' => true, 'attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('required' => false, 'attr' => array('autocomplete' => 'off')))
            ->getForm();

        return $this->render('AcmeRatingBundle:Contact:index.html.twig', array(
            'form' => $contactForm->createView(),
        ));
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
                $contactForm->get('email')->addError(new FormError('Kötelező e-mailt megadni.'));
                $isContactFormValid = FALSE;
            }
            
            if ( empty($contactFormData['lastName']) === TRUE ) {
                $contactForm->get('lastName')->addError(new FormError('Kötelező vezetéknevet megadni.'));
                $isContactFormValid = FALSE;
            }

            if ( $isContactFormValid === TRUE ) {
                $this->contactFormData = $contactFormData;
                $this->saveContactForm();
                return $this->redirect($this->generateUrl('contact_index'));
            }
        }
        
        return $this->render('AcmeRatingBundle:Contact:index.html.twig', array(
            'form' => $contactForm->createView(),
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
        $this->verifiedClientWithClientId = $this->getDoctrine()->getRepository('AcmeRatingBundle:VerifiedClient')->findOneByClientId($this->contactFormData['clientId']);
        $this->verifiedClientWithEmail = $this->getDoctrine()->getRepository('AcmeRatingBundle:VerifiedClient')->findOneByEmailAddress($this->contactFormData['email']);

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

    private function saveAsVerifiedClientIfBothClientIdAndEmailExistAndMatch() {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();

        $connection->update(
            'verified_client', 
            array(
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
            if ( empty($row['clientClientId']) == FALSE ) {
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
        $queryText = sprintf($this->autocompleteByEmailPrefixQueryText, $emailPrefix);
        $this->autocompleteByEmailPrefixStatement = $connection->executeQuery($queryText);
        $this->autocompleteByEmailPrefixStatement->execute();
    }

    private function addClientDataToAutocompleteForEmails($row) {
        $email = strtolower($row['clientEmailAddress']);
            
        $this->autocompleteDataByEmail[$email] = array(
            'clientId' => $row['clientClientId'],
            'firstName' => $row['clientFirstName'],
            'lastName' => $row['clientLastName'],
        );

        $this->addToAutocompleteForEmails($email);
    }

    private function addContactDataToAutocompleteForEmails($row) {
        $email = strtolower($row['contactEmailAddress']);

        if ( empty($this->autocompleteDataByEmail[$email]) == FALSE ) {
            return;
        }
        
        $this->autocompleteDataByEmail[$email] = array(
            'clientId' => '',
            'firstName' => $row['contactFirstName'],
            'lastName' => $row['contactLastName'],
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
        $queryText = sprintf($this->autocompleteByClientIdQueryText, $clientId);
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

        $rating = $contact->getRating();
        if ( empty($rating) === TRUE ) {
            $this->createRatingForContact($contact, $stars);
        }
        else if( $this->minsPassedSince($rating->getCreated()) < 21.0 ) {
            $this->updateRating($rating, $stars);
        }
        else {
            $stars = $contact->getRating()->getStars();
        }
        
        return $this->render('AcmeRatingBundle:Contact:vote.html.twig', array(
            'stars' => $stars,
            'contact' => $contact,
            'profileURL' => $this->getImageURL($contact->getRateable()),
        ));
    }
    
    private function createRatingForContact($contact, $stars) {
        $entityManager = $this->getDoctrine()->getManager();
        $rating = new Rating();
        $rating->setRateable($contact->getRateable());
        $rating->setStars($stars);
        $rating->setCreated(new \DateTime());
        $rating->setUpdated(new \DateTime());
        $entityManager->persist($rating);
        $entityManager->flush();
        
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        $connection->update('contact',
            array('rating_id' => $rating->getId()),
            array('id' => $contact->getId())
        );
    }
    
    private static function minsPassedSince($pastDatetime) {
        $currentDateTime = new \DateTime('now');
        $diff = $currentDateTime->getTimestamp() - $pastDatetime->getTimestamp();
        $diffInMins = (float)$diff/(float)60.0;
        return $diffInMins;
    }

    private function updateRating($rating, $stars) {
        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        $connection->update('rating',
            array('stars' => $stars, 'updated' => date('Y-m-d H:i:s')),
            array('id' => $rating->getId())
        );
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
