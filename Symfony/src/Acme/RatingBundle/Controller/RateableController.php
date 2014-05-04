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
        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findBy(array('rateable' => $rateable), array('created' => 'DESC'));

        $form = $this->createForm(new EditRateableForm(), $rateable);
        if ($identifier = $rateable->getIdentifier()) {
            $form->get('identifier')->setData($identifier->getAlphanumericValue());
        }

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                try {
                    $event = new ModifyRateableIdentifierEvent($rateable, $form->get('identifier')->getData());
                    $this->get('event_dispatcher')->dispatch('rating.modify.identifier', $event);
                    $this->getDoctrine()->getManager()->flush();
                } catch (ValidatorException $ex) {
                    $form->get('identifier')->addError(new FormError($ex->getMessage()));
                }
                $this->getDoctrine()->getManager()->flush();
            }
        }

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeRatingBundle:Rateable:profile.html.twig', array(
            'rateable' => $rateable,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverageWithTwoDecimals($ratings),
            'imageURL' => $this->getImageURL($rateable),
            'imageUploadForm' => $imageUploadForm->createView(),
            'editForm' => $form->createView(),
        ));
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
    }
    
    private function getRatingsAverageWithTwoDecimals($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();
        
        if ( count($ratings) == 0 )
            return 0.0;

        $average = (float)$ratingSum / (float)count($ratings);
        $average = round($average, 2);
        
        return number_format($average, 2, ',', ' ');
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
        
        $content = $this->renderView('AcmeRatingBundle:Rateable:index.html.twig', array(
            'rateable' => $rateable,
            'collection' => $rateable->getCollection(),
            'imageURL' => $this->getImageURL($rateable),
        ));

        return $content;
    }

    private function getActiveRateableById($id) {
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneBy(array(
            'id' => $id,
            'isActive' => true,
        ));

        if ( empty($rateable) || !$rateable->getCollection()->getOwners()->contains($this->getUser()) ) {
            throw $this->createNotFoundException('Rateable could not be found or inactive.');
        }

        return $rateable;
    }

    public function archiveAction(Request $request, Rateable $rateable) {
        $isActive = $request->get('isActive');
        $rateable->setIsActive($isActive);
        $rateable->getRateableUser()->setIsActive($isActive);
        $this->getDoctrine()->getManager()->flush();

        return new Response('OK');
    }
}
