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
            return null;
        }
        
        $rating = $this->getDoctrine()->getManager()->getRepository('AcmeRatingBundle:Rating')->find($request->request->get('ratingId'));
        if ( empty($rating) ) {
            throw $this->createNotFoundException('Rating not found by id.');
            return null;
        }
        
        $answer = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:Answer')->find($request->request->get('answerId'));
        if ( empty($answer) ) {
            throw $this->createNotFoundException('Answer not found by id.');
            return null;
        }
        
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
            // TODO: restore old code
            if ( 'Igazgatósági Ülés' === $rating->getRateable()->getName() ) {
                return $this->redirect($this->generateUrl('sub_rating_user_thank_you_custom'));
            }
            else {
                return $this->redirect($this->generateUrl('sub_rating_user_thank_you'));
            }
            /*
            return $this->redirect($this->generateUrl('sub_rating_user_thank_you'));
            */
        }
        if(NULL != $maximumQuestionCount && $maximumQuestionCount == $ratedQuestionsCount) {
            // TODO: restore old code
            if ( 'Igazgatósági Ülés' === $rating->getRateable()->getName() ) {
                return $this->redirect($this->generateUrl('sub_rating_user_thank_you_custom'));
            }
            else {
                return $this->redirect($this->generateUrl('sub_rating_user_thank_you'));
            }
            /*
            return $this->redirect($this->generateUrl('sub_rating_user_thank_you'));
            */
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
            )
        );
    }

    public function thankYouAction() {
        return $this->render('AcmeSubRatingBundle:User:thankYou.html.twig');
    }

    public function thankYouCustomAction() {
        return $this->render('AcmeSubRatingBundle:User:thankYouCustom.html.twig');
    }

}
