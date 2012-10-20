<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Acme\RatingBundle\Entity\Image;

class RateableController extends Controller
{
    public function indexAction($alphanumericValue)
    {
        $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
        return new Response($this->getRateablePageContents($rateable));
    }

    public function profileAction($id)
    {
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($id);
        if ( empty($rateable) === TRUE )
            throw $this->createNotFoundException('Rateable could not be found.');

        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findByRateable($rateable);

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeRatingBundle:Rateable:profile.html.twig', array(
            'rateable' => $rateable,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverage($ratings),
            'ratings' => $ratings,
            'imageURL' => $this->getImageURL($rateable),
            'imageUploadForm' => $imageUploadForm->createView(),
        ));
    }

    private function getImageURL($rateable)
    {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( empty($image) === FALSE )
            $imageURL = $image->getWebPath();
        
        return $imageURL;
    }

    public function uploadImageAction($id)
    {
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($id);
        if ( empty($rateable) === TRUE )
            throw $this->createNotFoundException('Rateable could not be found.');

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

    private function getRatingsAverage($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();
        
        if ( count($ratings) == 0 )
            return 0.0;

        return (float)$ratingSum / (float)count($ratings);
    }

    public function indexByIdAction($id)
    {
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($id);
        return new Response($this->getRateablePageContents($rateable));
    }

    private function getRateablePageContents($rateable)
    {
        if ( empty($rateable) === TRUE )
            throw $this->createNotFoundException('The rateable does not exists.');
        
        $content = $this->renderView('AcmeRatingBundle:Rateable:index.html.twig', array(
            'rateable' => $rateable,
            'collection' => $rateable->getCollection(),
        ));

        return $content;
    }
}
