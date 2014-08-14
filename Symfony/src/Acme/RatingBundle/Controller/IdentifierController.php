<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acme\RatingBundle\Utility\Validator;
use Symfony\Component\Form\FormError;

class IdentifierController extends Controller
{
    public function indexAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text', array('attr' => array('placeholder' => $this->get('translator')->trans('Enter Code Here', array(), 'identifier'), 'autocomplete' => 'off')))
            ->getForm();
        
        $signUpForm = $this->createFormBuilder($defaultData, array('csrf_protection' => false))
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
            ->add('alphanumericValue', 'text', array('attr' => array('placeholder' => 'Értékelő kód', 'autocomplete' => 'off')))
            ->getForm();
        
        $signUpForm = $this->createFormBuilder($defaultData, array('csrf_protection' => false))
            ->add('lastName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('firstName', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('email', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('company', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('message', 'textarea', array('attr' => array('autocomplete' => 'off')))
            ->getForm();
        
        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $alphanumericValue = $form->get('alphanumericValue')->getData();
                if ( empty($alphanumericValue) === TRUE ) {
                    $form->addError(new FormError('Nem adtál meg kódot'));                    
                    return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
                        'form'       => $form->createView(),
                        'signUpForm' => $signUpForm->createView(),
                    ));  
                }

                $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
                if ( empty($identifier) === TRUE ) {
                    $form->addError(new FormError('Az általad megadott kód nem létezik'));                      
                    return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
                        'form'       => $form->createView(),
                        'signUpForm' => $signUpForm->createView(),
                    ));  
                }
                
                $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
                if (empty($rateable) === FALSE) {
                    return $this->redirect($this->generateUrl('rateable_main', array('alphanumericValue' => $alphanumericValue)));
                }

                $collection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->findOneByIdentifier($identifier);
                if (empty($collection) === FALSE) {
                    return $this->redirect($this->generateUrl('rateable_collection_main', array('alphanumericValue' => $alphanumericValue)));
                }

                $form->addError(new FormError('Az általad megadott kód nem létezik'));                
                return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
                    'form'       => $form->createView(),
                    'signUpForm' => $signUpForm->createView(),
                ));  
            }
        }
    }
    
    public function signUpAction(Request $request) {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text', array('attr' => array('placeholder' => $this->get('translator')->trans('Enter Code Here', array(), 'identifier'), 'autocomplete' => 'off')))
            ->getForm();
        
        $signUpForm = $this->createFormBuilder($defaultData, array('csrf_protection' => false))
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
                $signUpForm->addError(new FormError('A megadott email cím nem megfelelő!'));
                return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
                    'form'       => $form->createView(),
                    'signUpForm' => $signUpForm->createView(),
                ));                
            }
            $this->get('session')->setFlash('success', $this->get('translator')->trans('Your message was sent!', array(), 'identifier'));
            $this->sendSignUpEmail($signUpForm);
            
            return $this->redirect($this->generateUrl('_welcome'));  
            
        }
    }
    
    private function sendSignUpEmail($signUpForm) {
        $signUpFormData = $signUpForm->getData();
        $message = \Swift_Message::newInstance()
            ->setSubject('[RateMe] SignUp')
            ->setFrom(array('dontreply@rate.me.uk' => 'RateMe'))
            ->setTo(array('tamas.t.marton@gmail.com', 'ilsinszkitamas@gmail.com'))
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
