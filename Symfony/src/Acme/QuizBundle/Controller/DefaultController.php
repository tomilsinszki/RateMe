<?php

namespace Acme\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Acme\QuizBundle\Entity\WrongAnswer;
use Acme\QuizBundle\Entity\Question;
use Acme\QuizBundle\Entity\Quiz;
use Acme\QuizBundle\Entity\QuizReply;
use Acme\QuizBundle\Entity\QuestionFile;

class DefaultController extends Controller {
    
    const FILLING_TIME_SECONDS = 180;
    /**
     * @Route("/quiz/questionnaire/{rateableCollectionId}")
     * @Template()
     */
    public function questionnaireAction($rateableCollectionId) {
        $rateableCollection = $this->getOwnedRateableCollectionById($rateableCollectionId);

        return array(
            'rateableCollectionId' => $rateableCollection->getId(),
            'rateableCollectionName' => $rateableCollection->getName(),
            'rateableCollections' => $this->get('security.context')->getToken()->getUser()->getOwnedCollections(),
            'questions' => $this->getDoctrine()->getRepository('AcmeQuizBundle:Question')->findAllJoinedWithWrongAnswers($rateableCollection->getId()),
        );
    }

    /**
     * @Route("/quiz/download/{rateableCollectionId}")
     */
    public function downloadAction($rateableCollectionId) {
        $excelService = $this->get('xls.service_xls2007');
        $excelService->excelObj->getProperties()->setCreator("RateMe")
                            ->setLastModifiedBy("RateMe")
                            ->setTitle("RateMe Kérdőív")
                            ->setSubject("RateMe Kérdőív kérdések és válaszaik")
                            ->setDescription("RateMe Kérdőív kérdések és válaszaik")
                            ->setKeywords("RateMe Kérdőív kérdések válaszok")
                            ->setCategory("RateMe Kérdőív kérdések és válaszaik");

        $excelService->excelObj->setActiveSheetIndex(0);
        $activeSheet = $excelService->excelObj->getActiveSheet();
        $activeSheet->setTitle('Simple');
        $headerNames = array('Kérdés', 'Helyes válasz', 'Egyéb válasz 1', 'Egyéb válasz 2');
        $activeSheet->fromArray($headerNames);
        $rowIterator = $activeSheet->getRowIterator();
        $rowIterator->next();

        $colNum = 0;
        $rowNum = 2;
        $doctrine = $this->getDoctrine();
        $questionRepo = $doctrine->getRepository('AcmeQuizBundle:Question');
        $questions = $questionRepo->findAllJoinedWithWrongAnswers($rateableCollectionId);
        foreach ($questions as $question) {
            $cell = $activeSheet->getCellByColumnAndRow($colNum, $rowNum);
            $cell->setValueExplicit($question->getCorrectAnswerText());
            $cell->setValueExplicit($question->getText());

            $colNum++;
            $cell = $activeSheet->getCellByColumnAndRow($colNum, $rowNum);
            $cell->setValueExplicit($question->getCorrectAnswerText());

            foreach ($question->getWrongAnswers() as $wrongAnswer) {
                $colNum++;
                $cell = $activeSheet->getCellByColumnAndRow($colNum, $rowNum);
                $cell->setValueExplicit($wrongAnswer->getText());
            }
            $colNum = 0;
            $rowNum++;
        }

        $response = $excelService->getResponse();
        $response->headers->set("Content-Description", "File Transfer");
        $response->headers->set('Expires', 0);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        $response->headers->set("Content-Transfer-Encoding", "Binary");
        $response->headers->set('Content-Disposition', 'attachment; filename="Kérdőív.xlsx"');
        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0, max-age=0');

        // If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        return $response;
    }

    /**
     * @Route("/quiz/upload")
     */
    public function uploadAction() {
        $errors = null;

        if ($this->getRequest()->isMethod('POST')) {
            $questionFile = new QuestionFile();
            $questionFile->setFile($this->getRequest()->files->get('file'));
            if (!$questionFile->isValid()) {
                return new Response(json_encode(array('invalid' => 'Kérlek, tölts fel egy helyes .xlsx fájlt!')), 200, array('Content-Type' => 'application/json'));
            }
            $questionFile->upload();
            $absPath = $questionFile->getAbsolutePath();
            $excelObj = null;
            switch ($questionFile->getExtension()) {
                case ('xlsx'):
                    $excelObj = $this->get('xls.load_xls2007')->load($absPath);
                    break;
                default:
                    break;
            }

            if ($excelObj) {
                $errors = $this->validateUploadedExcelFile($excelObj);
                if (empty($errors)) {
                    $errors = null;
                    $doctrine = $this->getDoctrine();
                    $questionRepo = $doctrine->getRepository('AcmeQuizBundle:Question');
                    $rateableCollectionId = $this->getRequest()->get('rateableCollectionId');
                    $questions = $questionRepo->getAllQuestionsWithWrongAnswersByText($rateableCollectionId);
                    $em = $doctrine->getManager();

                    $activeSheet = $excelObj->getSheet();
                    $rowIterator = $activeSheet->getRowIterator(2);
                    $answers = array();
                    
                    $query = $em->createQuery('SELECT rc FROM AcmeRatingBundle:RateableCollection rc');
                    $rateableCollections = $query->getResult();
                    $rateableCollectionsById = array();
                    foreach ($rateableCollections as $rateableCollection) {
                        $rateableCollectionsById[$rateableCollection->getId()] = $rateableCollection;
                    }
                    unset($rateableCollections);
                    
                    foreach ($rowIterator as $row) {
                		$cellIterator = $row->getCellIterator();
                		$qText = $cellIterator->current()->getValue();
                        $qTextKey = $qText;
                        
                		$question = null;
                		if (!isset($questions[$qTextKey])) {
                    		$question = new Question();
                    		$question->setText($qText);
                    		$questions[$qTextKey]['question'] = $question;
                    		$questions[$qTextKey]['wrongAnswers'] = array();
                		} else {
                		    $question = $questions[$qTextKey]['question'];
                		    $question->logUnDeleted();
                		}
                		$question->setRateableCollection($rateableCollectionsById[$rateableCollectionId]);

                		$cellIterator->next();
                		$answer = null;
                	    $aText = $cellIterator->current()->getValue();
                		if ($question->getCorrectAnswerText() !== $aText) {
                            $question->setCorrectAnswerText($aText);
                		}

                        $aTexts = array();
                		$cellIterator->next();
                		$aTexts[] = $cellIterator->current()->getValue();
                		$cellIterator->next();
                		$aTexts[] = $cellIterator->current()->getValue();

                		foreach ($aTexts as $aText) {
                            $aTextKey = $aText;
                		    if (isset($questions[$qTextKey]['wrongAnswers'][$aTextKey])) {
                		        unset($questions[$qTextKey]['wrongAnswers'][$aTextKey]);
                		    } else {
                                $answer = new WrongAnswer();
                                $answer->setQuestion($question);
                                $answer->setText($aText);
                                $em->persist($answer);
                		    }
                		}
                		foreach ($questions[$qTextKey]['wrongAnswers'] as $answer) {
    		                $answer->logDeleted();
    		                $em->persist($answer);
                		}

                		$em->persist($question);

                		unset($questions[$qTextKey]);
                    }

                    foreach ($questions as $question) {
                        $question['question']->logDeleted();
                    }

                    $em->flush();
                }
            }
        }

        return new Response(json_encode(array('errors' => $errors)), 200, array('Content-Type' => 'application/json'));
    }

    private function validateUploadedExcelFile($excelObj) {
        $errors = array();
        $activeSheet = $excelObj->getSheet();
        $rowIterator = $activeSheet->getRowIterator();
        $headerRow = $rowIterator->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $validHeader = array('Kérdés', 'Helyes válasz', 'Egyéb válasz 1', 'Egyéb válasz 2');
        $validHeaderOrigSize = count($validHeader);
        foreach ($cellIterator as $i => $headerCell) {
            if (!isset($validHeader[$i]) && $headerCell->getValue()) {
                $errors['HEADER'][] = 'A fejléc túl sok mezőt tartalmaz (' . ($i+1) . '. cella)!';
                break;
            }

            if (isset($validHeader[$i])) {
                if (strcasecmp($headerCell->getValue(), $validHeader[$i]) !== 0) {
                    $errors['HEADER'][] = 'A fejléc ' . ($i+1) . '. mezőjében a(z) "' . $validHeader[$i] . '" kell, hogy szerepeljen!';
                }

                unset($validHeader[$i]);
            }
        }

        $validHeaderSize = count($validHeader);
        if ($validHeaderSize === $validHeaderOrigSize) {
            $errors['HEADER'][] = 'A fejléc üres!';
        } elseif ($validHeaderSize === 1) {
            $errors['HEADER'][] = 'A fejlécből hiányzik a(z) "' . reset($validHeader) . '" mező!';
        } elseif ($validHeaderSize > 1) {
            $errors['HEADER'][] = 'A fejlécből hiányznak a következő mezők: "' . implode('","', $validHeader) . '"!';
        }

        $questionTexts = array();
        foreach ($rowIterator as $row) {
            $rowNum = $rowIterator->key();
            if (1 === $rowNum) {
                continue;
            }
    		$cellIterator = $row->getCellIterator();
    		$cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $i => $cell) {
                $cellValue = trim($cell->getValue());
                
                if ($i >= $validHeaderOrigSize and $cellValue) {
                    $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban a megengedettnél ('.$validHeaderOrigSize.') több mező van kitöltve (' . ($i+1) . '. cella)!';
                    break;
                }
                
                if ($i < $validHeaderOrigSize and ( $cellValue===null or $cellValue==='' )) {
                    $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban a(z) ' . ($i+1) . '. cella nincs kitöltve!';
                    break;
                }
                
                if ( 0 === $i ) {
                    if (in_array($cellValue, $questionTexts)) {
                        $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban lévő kérdés kétszer szerepel.';
                        break;
                    }

                    $questionTexts[] = $cellValue;
                }
                
                if ( 255 < mb_strlen($cellValue) ) {
                    $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban a(z) ' . ($i+1) . '. cella 255 karakternél hosszabb!';
                    break;
                }
            }
        }

        if (array_key_exists('QUESTIONS', $errors)) {
            $errors['QUESTIONS'] = array_reverse($errors['QUESTIONS']);
        }
        
        return $errors;
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

    /**
     * @Route("/quiz/save")
     */
    public function saveAction() {
        if ('POST' != $this->getRequest()->getMethod()) {
            throw $this->createNotFoundException('Expected POST method.');
            return null;
        }
        $quizRemainingSeconds = $this->getRequest()->get('quizRemainingTime');
        $quizElpasedSeconds   = DefaultController::FILLING_TIME_SECONDS - $quizRemainingSeconds;
        $user                 = $this->get('security.context')->getToken()->getUser();
        $rateable             = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
        $rateableId           = $rateable->getId();
        $quizData             = json_decode($this->getRequest()->get('quizData'));

        $connection = $this->getDoctrine()->getEntityManager()->getConnection();
        $now        = date("Y-m-d H:i:s");
        $connection->insert('quiz', array(
            'rateable_id'     => $rateableId,
            'created'         => $now,
            'elapsed_seconds' => $quizElpasedSeconds,
        ));

        $lastInsertedQuizId = $connection->lastInsertId();

        foreach ($quizData as $questionId => $wongAnswerId) {
            $connection->insert('quiz_reply', array(
                'quiz_id'               => $lastInsertedQuizId,
                'question_id'           => $questionId,
                'wrong_given_answer_id' => $wongAnswerId,                
            ));
        }

        return new Response('OK');
    }

    /**
     * @Route("/quiz/entrance")
     * @Template()
     */
    public function entranceAction() {
        if ( !$this->isCurrentUserAllowedToDoQuiz() ) {
            return $this->redirect($this->generateUrl('contact_index'));
        }

        return array();
    }

    /**
     * @Route("/quiz")
     * @Template()
     */
    public function indexAction() {
        if ( !$this->isCurrentUserAllowedToDoQuiz() ) {
            return $this->redirect($this->generateUrl('contact_index'));
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
        $questions = $this->getDoctrine()->getRepository('AcmeQuizBundle:Question')->find3RandomQuestionsNotShownInTheLast2Weeks($rateable);
        $questionsWithAnswers = $this->createQuestionsWithAnswersArray($questions);

        return array('rateableId' => $rateable->getId(), 'questions' => $questionsWithAnswers);
    }
    
    private function isCurrentUserAllowedToDoQuiz() {
        $user = $this->get('security.context')->getToken()->getUser();
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findOneByRateableUser($user);
        $questions = $this->getDoctrine()->getRepository('AcmeQuizBundle:Question')->find3RandomQuestionsNotShownInTheLast2Weeks($rateable);
        
        if ( 3 <= count($questions) ) {
            $completedQuizes = $this->getDoctrine()->getRepository('AcmeQuizBundle:Quiz')->createQueryBuilder('q')
                ->where('q.rateable = :rateable')
                ->setParameter('rateable', $rateable)
                ->orderBy('q.created', 'DESC')
                ->getQuery()
                ->getResult();
            
            if ( empty($completedQuizes) ) {
                return true;
            }
            
            $nowDate = new \DateTime();
            $nowDateString = $nowDate->format('Y-m-d');
            
            $lastCompletedQuiz = $completedQuizes[0];
            $lastCompletedQuizDateString = $lastCompletedQuiz->getCreated()->format('Y-m-d');
            
            if ( strlen($lastCompletedQuizDateString) === strlen($nowDateString) ) {
                if ( $lastCompletedQuiz->getCreated()->format('Y-m-d') != $nowDate->format('Y-m-d') ) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    private function createQuestionsWithAnswersArray($questions) {
        $questionsWithAnswers = array();

        foreach ($questions as  $question) {
            $questionId = $question->getId();
            $questionsWithAnswers[$questionId]['QUESTION'] = $question->getText();
            $questionsWithAnswers[$questionId]['CORRECT_ANSWER'] = $question->getCorrectAnswerText();
            $i = 1;
            foreach ($question->getWrongAnswers() as $wrongAnswer) {
                $questionsWithAnswers[$questionId]['WRONG_ANSWER'.$i] = $wrongAnswer->getText();
                $questionsWithAnswers[$questionId]['WRONG_ANSWER'.$i.'_ID'] = $wrongAnswer->getId();
                $i++;
            }
        }

        return $questionsWithAnswers;
    }
}

