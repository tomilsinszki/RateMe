<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateableController extends Controller
{
    public function indexAction($alphanumericValue)
    {
        $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByIdentifier($identifier);
        return new Response($this->getRateablePageContents($rateable));
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
