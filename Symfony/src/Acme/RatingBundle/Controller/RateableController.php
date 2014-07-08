<?php

namespace Acme\RatingBundle\Controller;

use Acme\RatingBundle\Entity\Identifier;
use Acme\RatingBundle\Event\ModifyIdentifierEvent;
use Acme\RatingBundle\Form\Type\EditRateableForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Acme\RatingBundle\Entity\Image;
use Acme\RatingBundle\Entity\Rateable;
use Acme\RatingBundle\Event\ModifyRateableIdentifierEvent;
use Symfony\Component\Validator\Exception\ValidatorException;
use Acme\RatingBundle\Utility\Validator;

class RateableController extends Controller
{
    public function indexAction($alphanumericValue)
    {
        $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
        return new Response($this->getRateablePageContents($rateable));
    }

    public function mismatchAction($rateableId)
    {
        return new Response($this->renderView('AcmeRatingBundle:Rateable:mismatch.html.twig', array()));
    }

    public function profileAction(Request $request, $id)
    {
        $rateable = $this->getActiveRateableById($id);
        
        $ownedCollections = $this->getUser()->getOwnedCollections();
        if (!$ownedCollections->contains($rateable->getCollection())) {
            throw $this->createNotFoundException('Rateable collection is not an owned collection for this user!');
        }

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();

        $form = $this->createForm(new EditRateableForm(), $rateable);
        $form->get('email')->setData($rateable->getRateableUser()->getEmail());
        if ($identifier = $rateable->getIdentifier()) {
            $form->get('identifier')->setData($identifier->getAlphanumericValue());
        }

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $email = $form->get('email')->getData();

                if ( !empty($email) and !Validator::isEmailAddressValid($email) ) {
                    $form->addError(new FormError("A megadott e-mail cím formátuma nem megfelelő!"));
                }
                else if ( $this->doesUserWithEmailAlreadyExist($email, $rateable) ) {
                    $form->addError(new FormError("A megadott e-mail cím már használatban van!"));
                }
                else {
                    $rateable->getRateableUser()->setEmail($email);
                    $this->getDoctrine()->getManager()->persist($rateable->getRateableUser());

                    try {
                        $event = new ModifyRateableIdentifierEvent($rateable, mb_strtoupper($form->get('identifier')->getData()));
                        $this->get('event_dispatcher')->dispatch('rating.modify.identifier', $event);
                        $this->getDoctrine()->getManager()->flush();
                    } catch (ValidatorException $ex) {
                        $form->get('identifier')->addError(new FormError($ex->getMessage()));
                    }
                    
                    $this->getDoctrine()->getManager()->flush();
                    
                    return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array(
                        'id' => $rateable->getCollection()->getId(),
                    )));
                }
            }
        }
        
        return $this->render('AcmeRatingBundle:Rateable:profile.html.twig', array(
            'rateable' => $rateable,
            'imageURL' => $this->getImageURL($rateable),
            'imageUploadForm' => $imageUploadForm->createView(),
            'editForm' => $form->createView(),
        ));
    }

    private function doesUserWithEmailAlreadyExist($email, $rateable) {
        $doesUserExist = false;

        if ( empty($email) ) {
            return false;
        }
        
        $user = $this->getDoctrine()->getRepository('AcmeUserBundle:User')->findOneBy(array('email' => $email));
        if ( !empty($user) ) {
            if ( $user !== $rateable->getRateableUser() ) {
                $doesUserExist = true;
            }
        }

        return $doesUserExist;
    }

    private function getImageURL($rateable)
    {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( !empty($image) ) {
            $imageURL = $image->getWebPath();
        }
        
        return $imageURL;
    }

    public function uploadImageAction($id)
    {
        $rateable = $this->getActiveRateableById($id);
        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $imageUploadForm->bind($this->getRequest());

            if ( $imageUploadForm->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $rateable->setImage($image);
                $rateable->logUpdated();
                $entityManager->persist($image);
                $entityManager->persist($rateable);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('rateable_profile_by_id', array('id' => $id)));
            }
        }
        
        $form = $this->createForm(new EditRateableForm(), $rateable);
        if ($identifier = $rateable->getIdentifier()) {
            $form->get('identifier')->setData($identifier->getAlphanumericValue());
        }
        
        return $this->render('AcmeRatingBundle:Rateable:profile.html.twig', array(
            'rateable' => $rateable,
            'imageURL' => $this->getImageURL($rateable),
            'imageUploadForm' => $imageUploadForm->createView(),
            'editForm' => $form->createView(),
        ));
    }
    
    public function indexByIdAction($id)
    {
        $rateable = $this->getActiveRateableById($id);
        return new Response($this->getRateablePageContents($rateable));
    }

    private function getRateablePageContents($rateable)
    {
        if ( empty($rateable) ) {
            throw $this->createNotFoundException('The rateable does not exists.');
        }

        if (!$rateable->getIsActive()) {
            return $this->renderView('AcmeRatingBundle:Rateable:archiveRateable.html.twig');
        }

        return $this->renderView('AcmeRatingBundle:Rateable:index.html.twig', array(
            'rateable' => $rateable,
            'collection' => $rateable->getCollection(),
            'imageURL' => $this->getImageURL($rateable),
        ));
    }

    private function getActiveRateableById($id) {
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneBy(array(
            'id' => $id,
            'isActive' => true,
        ));

        if (empty($rateable)) {
            throw $this->createNotFoundException('Rateable could not be found or inactive.');
        }

        return $rateable;
    }

    public function archiveAction(Request $request, Rateable $rateable) {
        $isActive = $request->get('isActive');
        $rateable->setIsActive($isActive);
        $rateable->getRateableUser()->setIsActive($isActive);
        $this->getDoctrine()->getManager()->flush();
        
        $content = $this->renderView('AcmeRatingBundle:RateableCollection:editRateables.html.twig', array(
            'collection' => $rateable->getCollection()
        ));
        
        return new Response($content);
    }
}
