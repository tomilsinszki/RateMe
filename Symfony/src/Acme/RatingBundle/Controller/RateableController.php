<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function profileAction($id)
    {
        $rateable = $this->getActiveRateableById($id);
        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findBy(array('rateable' => $rateable), array('created' => 'DESC'));

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeRatingBundle:Rateable:profile.html.twig', array(
            'rateable' => $rateable,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverageWithTwoDecimals($ratings),
            'ratings' => $ratings,
            'imageURL' => $this->getImageURL($rateable),
            'imageUploadForm' => $imageUploadForm->createView(),
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

        if ( empty($rateable) ) {
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
