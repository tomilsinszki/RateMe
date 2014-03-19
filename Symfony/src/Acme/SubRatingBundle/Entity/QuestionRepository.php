<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository {
    
    public function getNextQuestionForRating($rating) {
        $questions = $this->getQuestionsWithoutSubRatingsForRating($rating);
        if ( empty($questions) ) {
            return;
        }
        
        $questionOrderTypeName = $rating->getRateable()->getCollection()->getQuestionOrder()->getName();
        $nextQuestion = $this->getNextQuestionByOrderType($questions, $questionOrderTypeName);
        
        return $nextQuestion;
    }
    
    public function getRatedQuestionsCountByRating($rating) {
        $questions = $this->getQuestionsWithSubRatingsForRating($rating);        
        if(empty($questions)) {
            return 0;
        }        
        return count($questions);
    }
    
    public function getUnratedQuestionsCountByRating($rating) {
        $questions = $this->getQuestionsWithoutSubRatingsForRating($rating);
        if(empty($questions)) {
            return 0;
        }        
        return count($questions);
    }
    
    private function getNextQuestionByOrderType($questions, $questionOrderTypeName) {
        $nextQuestion = null;
        
        switch ($questionOrderTypeName) {
            case 'sequential':
                $nextQuestion = $questions[0];
            break;
            case 'random':
                $nextQuestion = $questions[rand(0, (count($questions)-1))];
            break;
            case 'weighted random':
                $nextQuestion = $this->getNextWeightedRandomQuestion($questions);
            break;
            case 'balanced':
                $nextQuestion = $this->getNextBalancedQuestion($questions);
            break;
            default:
                throw $this->createNotFoundException('Invalid question order type.');
            break;
        }
        
        return $nextQuestion;
    }
    
    private function getNextWeightedRandomQuestion($questions) {
        $weightQuestions = array();
        $questionCount = count($questions);
        $sequence = 1;
        
        foreach ($questions as $question) {            
            for($i = 0; $i < ($questionCount-$sequence+1); $i++) {
                $weightQuestions[] = $question;                
            }
            $sequence++;
        }
        
        return $weightQuestions[rand(0, (count($weightQuestions)-1))];
    }
    
    private function getNextBalancedQuestion($questions) {
        $minRatingCount    = null;
        $minRatingQuestion = null;
        
        foreach ($questions as $question) {
            $answerRatingCount = 0;
            foreach ($question->getAnswers() as $answer) {
                $answerRatingCount += count($answer->getSubRatings());                
            }
            
            if($answerRatingCount < $minRatingCount || null === $minRatingCount) {
                $minRatingCount    = $answerRatingCount;
                $minRatingQuestion = $question;
            }
        }
        
        return $minRatingQuestion;
    }
    
    private function getQuestionsWithoutSubRatingsForRating($rating) {
        $rateableCollection = $rating->getRateable()->getCollection();
        if ( empty($rateableCollection) ) {
            throw $this->createNotFoundException('Collection not found for rating.');
            return null;
        }
        
        $allQuestions = $this->createQueryBuilder('q')
            ->where('q.rateableCollection = :collection')
            ->setParameter('collection', $rateableCollection)
            ->andWhere('q.deleted IS NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery()
            ->getResult();

        $hasSubRatingsQuestions = $this->createQueryBuilder('q')
            ->leftJoin('q.answers', 'a')
            ->leftJoin('a.subRatings', 'sr')
            ->where('q.rateableCollection = :collection')
            ->setParameter('collection', $rateableCollection)
            ->andWhere('sr.rating = :rating')
            ->setParameter('rating', $rating)
            ->andWhere('q.deleted IS NULL')
            ->andWhere('sr IS NOT NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery()
            ->getResult();

        $questionsWithoutSubRatings = array();
        
        foreach($allQuestions as $question) {
            $hasSubRatings = false;

            foreach($hasSubRatingsQuestions as $hasSubRatingsQuestion) {
                if ( $hasSubRatingsQuestion->getId() == $question->getId() ) {
                    $hasSubRatings = true;
                }
            }

            if ( !$hasSubRatings ) {
                $questionsWithoutSubRatings[] = $question;
            }
        }
        
        return $questionsWithoutSubRatings;
    }
    
    private function getQuestionsWithSubRatingsForRating($rating) {
        $rateableCollection = $rating->getRateable()->getCollection();
        if(empty($rateableCollection)) {
            throw $this->createNotFoundException('Collection not found for rating.');
            return null;
        }
        
        $allQuestions = $this->createQueryBuilder('q')
            ->where('q.rateableCollection = :collection')
            ->setParameter('collection', $rateableCollection)
            ->andWhere('q.deleted IS NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery()
            ->getResult();

        $hasSubRatingsQuestions = $this->createQueryBuilder('q')
            ->leftJoin('q.answers', 'a')
            ->leftJoin('a.subRatings', 'sr')
            ->where('q.rateableCollection = :collection')
            ->setParameter('collection', $rateableCollection)
            ->andWhere('sr.rating = :rating')
            ->setParameter('rating', $rating)
            ->andWhere('q.deleted IS NULL')
            ->andWhere('sr IS NOT NULL')
            ->orderBy('q.sequence', 'ASC')
            ->getQuery()
            ->getResult();

        $questionsWithSubRatings = array();
        
        foreach($allQuestions as $question) {
            $hasSubRatings = false;
            foreach($hasSubRatingsQuestions as $hasSubRatingsQuestion) {
                if ( $hasSubRatingsQuestion->getId() == $question->getId() ) {
                    $hasSubRatings = true;
                }
            }
            if($hasSubRatings) {
                $questionsWithSubRatings[] = $question;
            }
        }        
        return $questionsWithSubRatings;
    }
}

