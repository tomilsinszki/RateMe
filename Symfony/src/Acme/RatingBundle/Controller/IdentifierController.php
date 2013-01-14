<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifierController extends Controller
{
    public function indexAction()
    {
        $ownedCollection = $this->getCollectionIfUserIsOwner();
        if ( empty($ownedCollection) === FALSE ) {
            return $this->redirect($this->generateUrl('rateable_collection_profile_by_id', array('id' => $ownedCollection->getId())));
        }
        
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumericValue', 'text')
            ->getForm();

        return $this->render('AcmeRatingBundle:Identifier:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    private function getCollectionIfUserIsOwner()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if ( empty($user) != FALSE ) {
            return null;
        }

        if ( $this->get('security.context')->isGranted('ROLE_MANAGER') != TRUE ) {
            return null;
        }

        $ownedCollections = $user->getOwnedCollections()->toArray();
        if ( count($ownedCollections) <= 0 ) {
            return null;
        }

        return array_pop($ownedCollections);
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

    public function notExistsAction()
    {
        return new Response('<html><body>Nincs ilyen kód.</body></html>');
    }
}
