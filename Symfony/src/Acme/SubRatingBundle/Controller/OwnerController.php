<?php

namespace Acme\SubRatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Acme\SubRatingBundle\Entity\Question;
use Acme\SubRatingBundle\Entity\Answer;

class OwnerController extends Controller
{
    const RATEABLE_TARGET_TYPE = 1;
    const COLLECTION_TARGET_TYPE = 2;
    
    public function indexAction($rateableCollectionId)
    {
        $ownedCollections = $this->get('security.context')->getToken()->getUser()->getOwnedCollections();
        $rateableCollection = $this->getOwnedRateableCollectionById($rateableCollectionId);
        $questions = $this->getQuestionsForCollection($rateableCollection);
        $questionTypes = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionType')->findAll();
        $questionOrders = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionOrder')->findAll();
        
        return $this->render(
            'AcmeSubRatingBundle:Owner:index.html.twig',
            array(
                'collection' => $rateableCollection,
                'ownedCollections' => $ownedCollections,
                'questions' => $questions,
                'questionTypes' => $questionTypes,
                'questionOrders' => $questionOrders,
            )
        );
    }
    
    private function getOwnedRateableCollectionById($rateableCollectionId) {
        $ownedCollections = $this->get('security.context')->getToken()->getUser()->getOwnedCollections();
        if ( empty($rateableCollectionId) ) {
            return $ownedCollections->first();
        }

        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($rateableCollectionId);
        if ( $ownedCollections->contains($rateableCollection) ) {
            return $rateableCollection;
        }

        throw $this->createNotFoundException('RateableCollection could not be found.');
        return null;
    }
    
    private function getQuestionsForCollection($rateableCollection) {
        $query = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->createQueryBuilder('q')
            ->where('q.rateableCollection = :collection')            
            ->setParameter('collection', $rateableCollection)
            ->andWhere('q.deleted IS NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery();
        
        return $query->getResult();
    }
    
    public function getQuestionTypesAction() {
        $data = array();

        $questionTypes = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionType')->findAll();
        foreach ($questionTypes as $questionType) {
            $data[] = array(
                'id' => $questionType->getId(),
                'name' => $this->get('translator')->trans($questionType->getName(), array(), 'questionType'),
            );
        }
        
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }
    
    public function questionOrderChangeAction(Request $request) {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $rateableCollection = $this->getOwnedRateableCollectionById($request->request->get('rateableCollectionId'));
        $questionOrder = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:QuestionOrder')->find($request->request->get('questionOrderId'));
        $rateableCollection->setQuestionOrder($questionOrder);
        $this->getDoctrine()->getManager()->flush();
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }
}
