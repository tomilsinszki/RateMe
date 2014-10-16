<?php

namespace Acme\QuizBundle\Entity;

use \DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * QuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuestionRepository extends EntityRepository {

    public function getAllQuestionsWithWrongAnswersByText($rateableCollectionId) {
        $questions = $this->findAllJoinedWithWrongAnswers($rateableCollectionId, true);
        $results = array();
        
        foreach ($questions as $question) {
            $qTextKey = trim($question->getText());
            $results[$qTextKey]['question'] = $question;
            $results[$qTextKey]['wrongAnswers'] = array();
            foreach ($question->getWrongAnswers() as $wrongAnswer) {
                $wrongAnswerTextKey = trim($wrongAnswer->getText());
                $results[$qTextKey]['wrongAnswers'][$wrongAnswerTextKey] = $wrongAnswer;
            }
        }
        
        return $results;
    }

    public function findAllJoinedWithWrongAnswers($rateableCollectionId, $deletedAlso = false) {
        $query = 'SELECT q, wA
                  FROM AcmeQuizBundle:Question q
                  LEFT JOIN q.wrongAnswers wA
                  WHERE q.rateableCollection = :id';
        if (!$deletedAlso) {
            $query .= ' AND q.deleted IS NULL AND wA.deleted IS NULL';
        }
        return $this->getEntityManager()
            ->createQuery($query)
            ->setParameter('id', $rateableCollectionId)
            ->getResult();
    }

    public function find3RandomQuestionsNotShownInTheLast2Weeks($rateable) {
        $randomIds = $this->find3RandomIdsOfQuestionsNotShownInTheLast2Weeks($rateable);
        if (!empty($randomIds)) {
            return $this->getEntityManager()
                ->createQuery(
                    'SELECT q, wA
                     FROM AcmeQuizBundle:Question q
                     JOIN q.wrongAnswers wA
                     WHERE q.id IN (:ids) and wA.deleted IS NULL'
                )
                ->setParameter('ids', $randomIds)
                ->getResult();
        }

        return array();
    }

    private function find3RandomIdsOfQuestionsNotShownInTheLast2Weeks($rateable) {
        /*
            The Native SQL query would be:
            SELECT * FROM question q
            WHERE q.id NOT IN
            (SELECT qzr.question_id FROM quiz_reply qzr
             JOIN quiz qz ON qz.id = qzr.quiz_id
             WHERE qz.rateable_id = 1 AND qz.created > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 WEEK)
            )
            AND q.rateable_collection_id = 1 AND q.deleted IS NULL;
        */
        $results = $this->getEntityManager()
            ->createQuery('SELECT q.id FROM AcmeQuizBundle:Question q
                          WHERE q.id NOT IN
                          (SELECT q2.id FROM AcmeQuizBundle:Question q2
                           JOIN q2.quizReplies qzr
                           JOIN qzr.quiz qz
                           WHERE qz.rateable = :rateableIdParam AND qz.created > :createdAtParam
                          )
                          AND q.rateableCollection = :reteableCollectionIdParam AND q.deleted IS NULL')
            ->setParameter('rateableIdParam', $rateable->getId())
            ->setParameter('reteableCollectionIdParam', $rateable->getCollection()->getId())
            ->setParameter('createdAtParam', new DateTime('-2 week'))
            ->getArrayResult();

        if (count($results) >= 3) {
            $randomKeys = array_rand($results, 3);
            return array(
                $results[$randomKeys[0]]['id'],
                $results[$randomKeys[1]]['id'],
                $results[$randomKeys[2]]['id']
            );
        }
        return array();
    }
}
