<?php

namespace Acme\SubRatingBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository {
    public function getNextQuestionForRating($rating) {
        $questions = $this->getQuestionsWithoutSubRatingsForRating($rating);

        if ( empty($questions) ) {
            return;
        }

        return $questions[0];
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
}

