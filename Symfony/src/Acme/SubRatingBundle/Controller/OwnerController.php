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

class OwnerController extends Controller
{
    const RATEABLE_TARGET_TYPE = 1;
    const COLLECTION_TARGET_TYPE = 2;
    
    public function ownerAction($rateableCollectionId)
    {
        $rateableCollection = $this->getOwnedRateableCollectionById($rateableCollectionId);
        $questions = $this->getQuestionsForCollection($rateableCollection);
        
        return $this->render(
            'AcmeSubRatingBundle:Owner:owner.html.twig',
            array(
                'ownedCollections' => $this->get('security.context')->getToken()->getUser()->getOwnedCollections(),
                'collection' => $rateableCollection,
                'questions' => $questions,
                'questionTypes' => $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionType')->findAll(),
                'questionOrders' => $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionOrder')->findAll(),
            )
        );
    }

    public function createQuestionFormAction() {
        return $this->render(
            'AcmeSubRatingBundle:Owner:createQuestionForm.html.twig',
            array('questionTypes' => $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionType')->findAll())
        );
    }

    public function createYesNoAnswersSubFormAction() {
        return $this->render(
            'AcmeSubRatingBundle:Owner:createYesNoAnswersSubForm.html.twig',
            array()
        );
    }

    public function createScaleAnswersSubFormAction() {
        return $this->render(
            'AcmeSubRatingBundle:Owner:createScaleAnswersSubForm.html.twig',
            array()
        );
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
        $questionOrder = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionOrder')->find($request->request->get('questionOrderId'));
        $rateableCollection->setQuestionOrder($questionOrder);
        $this->getDoctrine()->getManager()->flush();
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }

    public function createQuestionAction(Request $request) {
        if ( 'POST' != $request->getMethod() ) {
            throw $this->createNotFoundException('POST request expected.');
            return null;
        }
        
        $nextSequence = $this->resequenceQuestionsForRateableCollection($this->getOwnedRateableCollectionById($request->request->get('rateableCollectionId')));
        $request->request->set('sequence', $nextSequence);
        $request->request->set('isAnswerNaEnabled', ($request->request->get('isAnswerNaEnabled') === 'true'));
        
        if ( !$this->isCreateQuestionRequestValid($request) ) {
            throw $this->createNotFoundException('Request data invalid.');
            return null;
        }

        $question = $this->createQuestionObjectUsingRequest($request);
        
        $answers = array();
        if ( 1 == $request->request->get('typeId') ) {
            $answers = $this->createYesNoAnswerObjectsForQuestionUsingRequest($question, $request);
        }
        else if ( 2 == $request->request->get('typeId') ) {
            $answers = $this->createScaleAnswerObjectsForQuestionUsingRequest($question, $request);
        }
        
        $this->persistQuestionAndAnswers($question, $answers);
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }

    private function isCreateQuestionRequestValid($request) {
        if ( !$this->areRequestParametersNonEmpty($request, array('sequence', 'title', 'text', 'target', 'rateableCollectionId', 'typeId')) ) {
            return false;
        }

        if ( $request->request->get('isAnswerNaEnabled') ) {
            $answerNaText = $request->request->get('answerNaText');

            if ( empty($answerNaText) ) {
                return false;
            }
        }

        if ( 1 == $request->request->get('typeId') ) {
            return $this->areRequestParametersNonEmpty($request, array('answerYesText', 'answerNoText'));
        }
        else if ( 2 == $request->request->get('typeId') ) {
            return $this->areRequestParametersNonEmpty($request, array('answerOneText', 'answerTwoText', 'answerThreeText', 'answerFourText', 'answerFiveText'));
        }

        return false;
    }

    private function isModifyQuestionRequestValid($request) {
        if ( !$this->areRequestParametersNonEmpty($request, array('title', 'text', 'target', 'typeId')) ) {
            return false;
        }

        if ( $request->request->get('isAnswerNaEnabled') ) {
            $answerNaText = $request->request->get('answerNaText');

            if ( empty($answerNaText) ) {
                return false;
            }
        }
        
        if ( 1 == $request->request->get('typeId') ) {
            return $this->areRequestParametersNonEmpty($request, array('answerYesText', 'answerNoText'));
        }
        else if ( 2 == $request->request->get('typeId') ) {
            return $this->areRequestParametersNonEmpty($request, array('answerOneText', 'answerTwoText', 'answerThreeText', 'answerFourText', 'answerFiveText'));
        }
        
        return false;
    }

    private function areRequestParametersNonEmpty($request, $parameterNames) {
        foreach ($parameterNames as $parameterName) {
            $parameterValue = $request->request->get($parameterName);
            if ( empty($parameterValue) ) {
                return false;
            }
        }

        return true;
    }
    
    private function createQuestionObjectUsingRequest($request) {
        $question = new Question();
        $question->setSequence($request->request->get('sequence'));
        $question->setTitle($request->request->get('title'));
        $question->setText($request->request->get('text'));
        $question->setTarget($request->request->get('target'));
        $question->setRateableCollection($this->getOwnedRateableCollectionById($request->request->get('rateableCollectionId')));
        $question->setQuestionType($this->getDoctrine()->getRepository('AcmeSubRatingBundle:QuestionType')->find($request->request->get('typeId')));

        return $question;
    }

    private function createYesNoAnswerObjectsForQuestionUsingRequest($question, $request) {
        $answers = array();
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerYesText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), 'yes'));
        $answer->setQuestion($question);
        $answers[] = $answer;

        $answer = new Answer();
        $answer->setText($request->request->get('answerNoText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), 'no'));
        $answer->setQuestion($question);
        $answers[] = $answer;

        $answer = new Answer();
        $answer->setText($request->request->get('answerNaText'));
        $answer->setIsEnabled($request->request->get('isAnswerNaEnabled'));
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), 'n/a'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        return $answers;
    }

    private function createScaleAnswerObjectsForQuestionUsingRequest($question, $request) {
        $answers = array();
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerOneText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), '1'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerTwoText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), '2'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerThreeText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), '3'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerFourText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), '4'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerFiveText'));
        $answer->setIsEnabled(true);
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), '5'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        $answer = new Answer();
        $answer->setText($request->request->get('answerNaText'));
        $answer->setIsEnabled($request->request->get('isAnswerNaEnabled'));
        $answer->setAnswerType($this->getAnswerTypeByQuestionTypeAndName($question->getQuestionType(), 'n/a'));
        $answer->setQuestion($question);
        $answers[] = $answer;
        
        return $answers;
    }

    private function persistQuestionAndAnswers($question, $answers) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        
        foreach($answers as $answer) {
            $em->persist($answer);
        }
        
        $em->flush();
    }
    
    private function getAnswerTypeByQuestionTypeAndName($questionType, $name) {
        $query = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:AnswerType')->createQueryBuilder('at')
            ->leftJoin('at.questionType', 'qt')
            ->where('qt.id = :questionTypeId')
            ->andWhere('at.name = :name')
            ->setParameter('questionTypeId', $questionType->getId())
            ->setParameter('name', $name)
            ->getQuery();
        
        return $query->getSingleResult();
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

    private function resequenceQuestionsForRateableCollection($rateableCollection) {
        $query = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Question')->createQueryBuilder('q')
            ->where('q.rateableCollection = :collection')            
            ->setParameter('collection', $rateableCollection)
            ->andWhere('q.deleted IS NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery();

        $questions = $query->getResult();
        
        $sequence = 1;
        foreach($questions as $question) {
            $question->setSequence($sequence);
            ++$sequence;
            $this->getDoctrine()->getManager()->persist($question);
        }
        
        $this->getDoctrine()->getManager()->flush();

        return $sequence;
    }

    public function deleteQuestionAction(Request $request) {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $question = $this->getNonDeletedQuestionForRequest($request);
        $rateableCollection = $this->getOwnedRateableCollectionById($question->getRateableCollection()->getId());
        
        $question->setSequence(null);
        $question->logDeleted();
        $this->getDoctrine()->getManager()->persist($question);
        $this->getDoctrine()->getManager()->flush();
        
        $this->resequenceQuestionsForRateableCollection($rateableCollection);
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }
    
    public function questionSequenceChangeAction(Request $request) {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $rateableCollection = $this->getOwnedRateableCollectionById($request->request->get('rateableCollectionId'));
        $questions = $this->getQuestionsForCollection($rateableCollection);
        foreach($questions as $question) {
            $question->setSequence(null);
            $this->getDoctrine()->getManager()->persist($question);
        }
        $this->getDoctrine()->getManager()->flush();
        
        $sequence = 1;
        $questionIds = $request->request->get('questionIdsInNewSequence');
        foreach ($questionIds as $questionId) {
            $question = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:Question')->find($questionId);
            $question->setSequence($sequence);
            $this->getDoctrine()->getManager()->persist($question);
            ++$sequence;
        }
        $this->getDoctrine()->getManager()->flush();
        
        $this->resequenceQuestionsForRateableCollection($rateableCollection);
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }

    public function getAnswersAction(Request $request) {
        if ('POST' != $request->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        
        $question = $this->getNonDeletedQuestionForRequest($request);
        $this->getOwnedRateableCollectionById($question->getRateableCollection()->getId());
        
        if ( $this->doSubRatingsExistForQuestion($question) ) {
            return $this->render(
                'AcmeSubRatingBundle:Owner:viewAnswersForm.html.twig',
                array('question' => $question)
            );
        }

        return $this->render(
            'AcmeSubRatingBundle:Owner:modifyAnswersForm.html.twig',
            array('question' => $question)
        );
    }

    public function modifyQuestionAction(Request $request) {
        if ( 'POST' != $request->getMethod() ) {
            throw $this->createNotFoundException('POST request expected.');
            return null;
        }
        
        $request->request->set('isAnswerNaEnabled', ($request->request->get('isAnswerNaEnabled') === 'true'));
        
        if ( !$this->isModifyQuestionRequestValid($request) ) {
            throw $this->createNotFoundException('Request data invalid.');
            return null;
        }

        $question = $this->getNonDeletedQuestionForRequest($request);
        $this->getOwnedRateableCollectionById($question->getRateableCollection()->getId());

        if ( $this->doSubRatingsExistForQuestion($question) ) {
            throw $this->createNotFoundException('Cannot modify question. There are already sub ratings for this question.');
            return null;
        }

        if ( $question->getQuestionType()->getId() == intval($request->request->get('typeId')) ) {
            $this->modifyQuestionAndAnswersIfQuestionTypeDidNotChange($question, $request);
        }
        else {
            $this->modifyQuestionAndAnswersIfQuestionTypeDidChange($question, $request);
        }
        
        return new Response(json_encode(array()), 200, array('Content-Type' => 'application/json'));
    }

    private function modifyQuestionAndAnswersIfQuestionTypeDidNotChange($question, $request) {
        $answers = array();

        if ( 'yes/no' == $question->getQuestionType()->getName() ) {
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, 'yes')->setText($request->request->get('answerYesText'))->logUpdated();
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, 'no')->setText($request->request->get('answerNoText'))->logUpdated();
        }
        else if ( 'scale' == $question->getQuestionType()->getName() ) {
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, '1')->setText($request->request->get('answerOneText'))->logUpdated();
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, '2')->setText($request->request->get('answerTwoText'))->logUpdated();
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, '3')->setText($request->request->get('answerThreeText'))->logUpdated();
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, '4')->setText($request->request->get('answerFourText'))->logUpdated();
            $answers[] = $this->getAnswerForQuestionByAnswerTypeName($question, '5')->setText($request->request->get('answerFiveText'))->logUpdated();
        }

        $answer = $this->getAnswerForQuestionByAnswerTypeName($question, 'n/a');
        $answer->setText($request->request->get('answerNaText'));
        $answer->setIsEnabled($request->request->get('isAnswerNaEnabled'));
        $answer->logUpdated();
        $answers[] = $answer;
        
        $question->setTitle($request->request->get('title'));
        $question->setText($request->request->get('text'));
        $question->setTarget($request->request->get('target'));
        $question->logUpdated();

        $this->persistQuestionAndAnswers($question, $answers);
    }

    private function modifyQuestionAndAnswersIfQuestionTypeDidChange($question, $request) {
        $this->deleteAnswersForQuestion($question);
        
        $questionType = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:QuestionType')->find($request->request->get('typeId'));
        $answers = array();
        
        $question->setTitle($request->request->get('title'));
        $question->setText($request->request->get('text'));
        $question->setTarget($request->request->get('target'));
        $question->setQuestionType($questionType);
        $question->logUpdated();

        if ( 'yes/no' == $question->getQuestionType()->getName() ) {
            $answers = $this->createYesNoAnswerObjectsForQuestionUsingRequest($question, $request);
        }
        else if ( 'scale' == $question->getQuestionType()->getName() ) {
            $answers = $this->createScaleAnswerObjectsForQuestionUsingRequest($question, $request);
        }
        
        $this->persistQuestionAndAnswers($question, $answers);
    }
    
    private function deleteAnswersForQuestion($question) {
        $em = $this->getDoctrine()->getManager();

        foreach ($question->getAnswers() as $answer) {
            $em->remove($answer);
        }
        
        $em->flush();
    }

    private function getAnswerForQuestionByAnswerTypeName($question, $answerTypeName) {
        $query = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:Answer')->createQueryBuilder('a')
            ->leftJoin('a.answerType', 'at')
            ->leftJoin('a.question', 'q')
            ->where('at.name = :name')
            ->andWhere('q.id = :questionId')
            ->andWhere('q.deleted IS NULL')
            ->setParameter('name', $answerTypeName)
            ->setParameter('questionId', $question->getId())
            ->getQuery();
        
        return $query->getSingleResult();
    }

    private function getNonDeletedQuestionForRequest($request) {
        $question = $this->getDoctrine()->getManager()->getRepository('AcmeSubRatingBundle:Question')->findOneBy(array(
            'id' => $request->request->get('questionId'),
            'deleted' => null
        ));
        
        if ( empty($question) ) {
            throw $this->createNotFoundException('No question found for ID.');
            return null;
        }

        return $question;
    }

    private function doSubRatingsExistForQuestion($question) {
        $query = $this->getDoctrine()->getRepository('AcmeSubRatingBundle:SubRating')->createQueryBuilder('sr')
            ->leftJoin('sr.answer', 'a')
            ->leftJoin('a.question', 'q')
            ->where('q.id = :questionId')
            ->andWhere('q.deleted IS NULL')
            ->setParameter('questionId', $question->getId())
            ->getQuery();

        $subRatings = $query->getResult();
        return !empty($subRatings);
    }
}
