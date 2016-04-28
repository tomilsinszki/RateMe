<?php

namespace Acme\SubRatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Acme\SubRatingBundle\Entity\Question;
use Acme\SubRatingBundle\Entity\QuestionType;
use Acme\SubRatingBundle\Entity\Answer;
use Acme\SubRatingBundle\Entity\AnswerType;
use Acme\SubRatingBundle\Entity\SubRating;

class UserController extends Controller {

    public function saveSubRatingAndShowNextQuestionAction(Request $request) {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
        }
        
        $rating = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:Rating')->find($request->request->get('ratingId'));
        if ( empty($rating) ) {
            throw $this->createNotFoundException('Rating not found by id.');
        }
        
        $answer = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:Answer')->find($request->request->get('answerId'));
        if ( empty($answer) ) {
            throw $this->createNotFoundException('Answer not found by id.');
        }

        $company = $rating->getRateable()->getCollection()->getCompany();
        
        $subRating = new SubRating();
        $subRating->setRating($rating);
        $subRating->setAnswer($answer);
        $this->getDoctrine()->getManager()->persist($subRating);
        $this->getDoctrine()->getManager()->flush();
        
        $contact               = $this->getDoctrine()->getRepository('AcmeRatingBundle:Contact')->findOneByRating($rating);
        $question              = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getNextQuestionForRating($rating);
        $maximumQuestionCount  = $rating->getRateable()->getCollection()->getMaxQuestionCount();
        $ratedQuestionsCount   = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getRatedQuestionsCountByRating($rating);        
        $unratedQuestionsCount = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->getUnratedQuestionsCountByRating($rating);

        if(empty($question)) {
            return $this->redirect($this->generateUrl('sub_rating_user_thank_you', array(
                'companyId' => $company->getId(),
                'rateableCollectionId' => $rating->getRateable()->getCollection()->getId(),
                'ratingId' => $rating->getId(),
            )));
        }
        if(NULL != $maximumQuestionCount && $maximumQuestionCount == $ratedQuestionsCount) {
            return $this->redirect($this->generateUrl('sub_rating_user_thank_you', array(
                'companyId' => $company->getId(),
                'rateableCollectionId' => $rating->getRateable()->getCollection()->getId(),
                'ratingId' => $rating->getId(),
            )));
        }
        if(NULL != $maximumQuestionCount  && ($unratedQuestionsCount + $ratedQuestionsCount) > $maximumQuestionCount) {
            $unratedQuestionsCount = $maximumQuestionCount - $ratedQuestionsCount;
        }        
        return $this->render(
            'AcmeSubRatingBundle:User:subRatingForm.html.twig',
            array(
                'rating'         => $rating,
                'question'       => $question,
                'questionsCount' => $unratedQuestionsCount - 1,
                'contact'        => $contact,
                'company'        => $company,
            )
        );
    }

    public function thankYouAction(Request $request, $companyId, $rateableCollectionId, $ratingId) {
        $company = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:Company')->find($companyId);
        if ( empty($company) ) {
            throw $this->createNotFoundException('Company not found by id.');
        }

        $rateableCollection = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:RateableCollection')->find($rateableCollectionId);
        if ( empty($rateableCollection) ) {
            throw $this->createNotFoundException('Rateable collection not found by id.');
        }

        $rating  = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:Rating')->find($ratingId);
        if ( empty($rating) ) {
            throw $this->createNotFoundException('Rating not found by id.');
        }

        return $this->render('AcmeSubRatingBundle:User:thankYou.html.twig', array('company' => $company, 'rateableCollection' => $rateableCollection, 'rating' => $rating));
    }

}
