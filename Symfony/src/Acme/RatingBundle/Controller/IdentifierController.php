<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acme\RatingBundle\Utility\Validator;

class IdentifierController extends Controller
{
    public function indexAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text', array('attr' => array('placeholder' => $this->get('translator')->trans('Enter Code Here', array(), 'identifier'), 'autocomplete' => 'off')))
            ->getForm();
        
        $signUpForm = $this->createFormBuilder($defaultData)
            ->add('lastName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('email', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('company', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('message', 'textarea', array('attr' => array('autocomplete' => 'off')))
            ->getForm();

        return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
            'form'       => $form->createView(),
            'signUpForm' => $signUpForm->createView(),
        ));
    }

    public function searchAction(Request $request)
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text', array('attr' => array('placeholder' => 'Add meg a 4 jegyű kódot', 'autocomplete' => 'off')))
            ->getForm();
        
        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $alphanumericValue = $form->get('alphanumericValue')->getData();
                if ( empty($alphanumericValue) === TRUE ) {
                    $this->get('session')->setFlash('notice', 'Nem adtál meg kódot');
                    return $this->redirect($this->generateUrl('_welcome'));
                }

                $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
                if ( empty($identifier) === TRUE ) {
                    $this->get('session')->setFlash('notice', 'Az általad megadott kód nem létezik');
                    return $this->redirect($this->generateUrl('_welcome'));
                }
                
                $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
                if ( empty($rateable) === FALSE )
                    return $this->redirect($this->generateUrl('rateable_main', array('alphanumericValue' => $alphanumericValue)));
                
                $collection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->findOneByIdentifier($identifier);
                if ( empty($collection) === FALSE )
                    return $this->redirect($this->generateUrl('rateable_collection_main', array('alphanumericValue' => $alphanumericValue)));
                
                $this->get('session')->setFlash('notice', 'Az általad megadott kód nem létezik');
                return $this->redirect($this->generateUrl('_welcome'));
            }
        }
    }
    
    public function signUpAction(Request $request) {
        $defaultData = array();
        $signUpForm = $this->createFormBuilder($defaultData)
            ->add('lastName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('email', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('company', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('message', 'textarea', array('attr' => array('autocomplete' => 'off')))
            ->getForm();
        if ( $request->isMethod('POST') ) {
            $signUpForm->bind($request);
            $email = $signUpForm->get('email')->getData();
            if(FALSE === Validator::isEmailAddressValid($email)) {
                $this->get('session')->setFlash('notice', 'A megadott email cím nem megfelelő!');
                return $this->redirect($this->generateUrl('_welcome'));
            }
            $this->sendSignUpEmail($signUpForm);
            return $this->redirect($this->generateUrl('_welcome'));
        }
    }
    
    private function sendSignUpEmail($signUpForm) {
        $signUpFormData = $signUpForm->getData();
        $message = \Swift_Message::newInstance()
            ->setSubject('[RateMe] SignUp')
            ->setFrom(array('dontreply@rate.me.uk' => 'RateMe'))
            ->setTo(array('cshorv@gmail.com', 'tamas.t.marton@gmail.com', 'ilsinszkitamas@gmail.com'))
            ->addBcc('rateme.archive@gmail.com')
            ->setBody(
                $this->renderView(
                    'AcmeRatingBundle:Identifier:signUpEmail.html.twig',
                    array('signUpFormData' => $signUpFormData)
                )
            );
        $this->get('mailer')->send($message);
    }
}
