<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    private $autocompleteByEmailPrefixQueryText = 
        'SELECT 
            co.email_address,
            cl.client_id,
            cl.first_name,
            cl.last_name 
        FROM contact co 
        LEFT JOIN client cl ON co.client_id=cl.id 
        WHERE co.email_address LIKE "%1$s%%"';
    
    private $autocompleteByEmailPrefixStatement = null;
    private $autocompleteForEmails = array();
    private $autocompleteDataByEmail = array();

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
            $this->addToAutocompleteForEmails($row);
            $this->addtoAutocompleteDataByEmail($row);
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

    private function addToAutocompleteForEmails($row) {
        array_push($this->autocompleteForEmails, strtolower($row['email_address']));
    }

    private function addtoAutocompleteDataByEmail($row) {
        $email = strtolower($row['email_address']);
            
        $this->autocompleteDataByEmail[$email] = array(
            'clientId' => $row['client_id'],
            'firstName' => $row['first_name'],
            'lastName' => $row['last_name'],
        );
    }
}
