<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateableCollectionController extends Controller
{
    public function indexAction($alphanumericValue)
    {
        $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);

        $collection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->findOneByIdentifier($identifier);
        if ( empty($collection) === TRUE )
            throw $this->createNotFoundException('The rateable collection does not exists.');

        $content = $this->renderView('AcmeRatingBundle:RateableCollection:index.html.twig', array(
            'collection' => $collection,
            'rateables' => $collection->getRateables(),
        ));

        return new Response($content);
    }

    public function publicProfileAction($id)
    {
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($id);
        if ( empty($rateableCollection) === TRUE )
            throw $this->createNotFoundException('RateableCollection could not be found.');

        $rateables = $this->getRateablesWithAverageAndCount($rateableCollection->getRateables());
        $ratings = $this->getRatingsForRateables($rateableCollection->getRateables());
        
        return $this->render('AcmeRatingBundle:RateableCollection:publicProfile.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'rateablesData' => $this->getRateablesWithAverageAndCount($rateableCollection->getRateables()),
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverage($ratings),
        ));
    }

    private function getRateablesWithAverageAndCount($rateables)
    {
        $rateablesById = array();
       
        foreach($rateables AS $rateable) {
            $rateablesById[$rateable->getId()] = array();
            $rateablesById[$rateable->getId()]['rateable'] = $rateable;
            $rateablesById[$rateable->getId()]['ratingsCount'] = count($rateable->getRatings());
            $rateablesById[$rateable->getId()]['ratingsAverage'] = $this->getRatingsAverage($rateable->getRatings());
        }
        
        return $rateablesById;
    }

    public function profileAction($id)
    {
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($id);
        if ( empty($rateableCollection) === TRUE )
            throw $this->createNotFoundException('RateableCollection could not be found.');

        $ratings = $this->getRatingsForRateables($rateableCollection->getRateables());
        
        return $this->render('AcmeRatingBundle:RateableCollection:profile.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'ratings' => $ratings,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverage($ratings),
        ));
    }

    private function getRatingsForRateables($rateables)
    {
        $ratings = array();

        foreach($rateables AS $rateable) {
            foreach($rateable->getRatings() AS $rating) {
                array_push($ratings, $rating);
            }
        }

        return $ratings;
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
}
