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
                // TODO: check if alphanumeric value exists, if not error page

                // TODO: get alphanumeric value
                $alphanumericValue = $form->get('alphanumericValue')->getData();

                $identifier = $this->getDoctrine()
                    ->getRepository('AcmeRatingBundle:Identifier')
                    ->findOneByAlphanumericValue($alphanumericValue);

                if ( empty($identifier) === TRUE )
                    return $this->redirect($this->generateUrl('identifier_not_found'));

                
                /*
                else {
                }
                */

                //return $this->redirect($this->generateUrl('acme_user_registration'));
            }
        }
    }

    public function notExistsAction()
    {
        return new Response('<html><body>Nincs ilyen kód.</body></html>');
    }
}
