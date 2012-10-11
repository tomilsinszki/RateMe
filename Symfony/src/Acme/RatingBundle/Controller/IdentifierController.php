<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifierController extends Controller
{
    public function indexAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text')
            ->getForm();

        return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function searchAction(Request $request)
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text')
            ->getForm();
        
        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $alphanumericValue = $form->get('alphanumericValue')->getData();
                $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
                
                $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
                if ( empty($rateable) === FALSE )
                    return $this->redirect($this->generateUrl('rateable_main', array('alphanumericValue' => $alphanumericValue)));
                
                $collection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->findOneByIdentifier($identifier);
                if ( empty($collection) === FALSE )
                    return $this->redirect($this->generateUrl('rateable_collection_main', array('alphanumericValue' => $alphanumericValue)));
                
                return $this->redirect($this->generateUrl('identifier_not_found'));
            }
        }
    }

    public function notExistsAction()
    {
        return new Response('<html><body>Nincs ilyen kód.</body></html>');
    }
}
