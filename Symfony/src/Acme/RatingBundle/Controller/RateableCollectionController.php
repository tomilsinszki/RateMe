<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Acme\RatingBundle\Entity\Rateable;
use Acme\RatingBundle\Entity\Image;

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

    public function editAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        $rateables = $rateableCollection->getRateables();
        $collectionImageURL = $this->getImageURLForCollection($rateableCollection);

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeRatingBundle:RateableCollection:edit.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'rateables' => $rateables,
            'imageUploadForm' => $imageUploadForm->createView(),
            'collectionImageURL' => $collectionImageURL,
        ));
    }

    private function getImageURLForCollection($rateableCollection)
    {
        $collectionImageURL = null;
        $collectionImage = $rateableCollection->getImage();
        if ( empty($collectionImage) === FALSE )
            $collectionImageURL = $collectionImage->getWebPath();
        
        return $collectionImageURL;
    }

    public function uploadImageAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        
        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $imageUploadForm->bind($this->getRequest());

            if ( $imageUploadForm->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $rateableCollection->setImage($image);
                $rateableCollection->logUpdated();
                $entityManager->persist($image);
                $entityManager->persist($rateableCollection);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $id)));
            }
        }
    }

    private function getRateableCollectionById($id)
    {
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($id);
        if ( empty($rateableCollection) === TRUE )
            throw $this->createNotFoundException('RateableCollection could not be found.');
        
        return $rateableCollection;
    }

    public function updateAction()
    {
        $collection = $this->getRateableCollectionFromRequest();

        $collection->setName($this->getRequest()->request->get('collectionName'));
        $collection->setForeignURL($this->getRequest()->request->get('collectionForeignURL'));
        $collection->logUpdated();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($collection);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $collection->getId())));
    }

    public function newRateableForCollectionAction()
    {
        $collection = $this->getRateableCollectionFromRequest();

        $rateable = new Rateable();
        $rateable->setName($this->getRequest()->request->get('rateableName'));
        $rateable->setTypeName($this->getRequest()->request->get('rateableTypeName'));
        $rateable->setImageURL('www.index.hu');
        $rateable->setCollection($collection);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rateable);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $collection->getId())));
    }

    private function getRateableCollectionFromRequest()
    {
        $rateableCollectionId = $this->getRequest()->request->get('rateableCollectionId');
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($rateableCollectionId);
        if ( empty($rateableCollection) === TRUE )
            throw $this->createNotFoundException('The rateable collection does not exists.');

        return $rateableCollection;
    }

    public function updateRateableForCollectionAction()
    {
        $rateable = $this->getRateableFromRequest();
        
        $rateable->setName($this->getRequest()->request->get('rateableName'));
        $rateable->setTypeName($this->getRequest()->request->get('rateableTypeName'));
        $rateable->logUpdated();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rateable);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $this->getRequest()->request->get('rateableCollectionId'))));
    }

    private function getRateableFromRequest()
    {
        $rateableId = $this->getRequest()->request->get('rateableId');
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($rateableId);
        if ( empty($rateable) === TRUE )
            throw $this->createNotFoundException('The rateable does not exists.');

        return $rateable;
    }
}
