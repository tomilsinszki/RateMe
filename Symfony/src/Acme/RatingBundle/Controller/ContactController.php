<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
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

    public function indexAction(Request $request)
    {
        $defaultData = array();
        $contactForm = $this->createFormBuilder($defaultData)
            ->add('email', 'email', array('attr' => array('autocomplete' => 'off')))
            ->add('clientId', 'text')
            ->add('lastName', 'text')
            ->add('firstName', 'text')
            ->getForm();

        return $this->render('AcmeRatingBundle:Contact:index.html.twig', array(
            'form' => $contactForm->createView(),
        ));
    }
    
    public function newAction()
    {
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
        array_push($this->autocompleteForEmails, strtolower($email));
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
            'emailAddress' => $row['emailAddress'],
        );

        $this->addToAutocompleteForClientId($clientId);
    }

    private function addToAutocompleteForClientId($clientId) {
        array_push($this->autocompleteForClientIds, $clientId);
    }
}
