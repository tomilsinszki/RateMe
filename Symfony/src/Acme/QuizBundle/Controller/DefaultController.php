<?php

namespace Acme\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Acme\QuizBundle\Entity\Answer;
use Acme\QuizBundle\Entity\Question;
use Acme\QuizBundle\Entity\Quiz;
use Acme\QuizBundle\Entity\QuestionFile;

class DefaultController extends Controller {

    /**
     * @Route("/quiz")
     * @Template()
     */
    public function uploadAction() {
        $questionFile = new QuestionFile();
        $questionUploadForm = $this->createFormBuilder($questionFile)->add('file', 'file', array('required' => true))->getForm();

        $errors = null;
        $rowNum = 0;

        if ($this->getRequest()->isMethod('POST')) {
            //$questionUploadForm->handleRequest($this->getRequest());
            $questionUploadForm->bind($this->getRequest());
            if ($questionUploadForm->isValid()) {
                $questionFile->upload();
                $absPath = $questionFile->getAbsolutePath();
                $excelObj = null;
                switch ($questionFile->getExtension()) {
                    case ('xlsx'):
                        $excelObj = $this->get('xls.load_xls2007')->load($absPath);
                        break;
                    case ('xls'):
                        $excelObj = $this->get('xls.load_xls5')->load($absPath);
                        break;
                    default:
                        break;
                }

                if ($excelObj) {
                    $errors = $this->validateUploadedExcelFile($excelObj);
                    if (empty($errors)) {
                        // delete previous quiz data (cascades will take place...)
                        $em = $this->getDoctrine()->getManager();
                        $query = $em->createQuery('DELETE AcmeQuizBundle:Answer a');
                        $query->execute();

                        // save questions into the database
                        $activeSheet = $excelObj->getSheet();
                        $rowIterator = $activeSheet->getRowIterator(2);
                        $answers = array();
                        foreach ($rowIterator as $row) {
                    		$cellIterator = $row->getCellIterator();
                    		$question = new Question();
                    		$question->setText($cellIterator->current()->getValue());
                    		$cellIterator->next();
                    		$answer = null;
                    	    $answerText = $cellIterator->current()->getValue();
                    		if (!isset($answers[$answerText])) {
                                $answer = new Answer();
                                $answer->setText($answerText);
                                $em->persist($answer);
                                $answers[$answerText] = $answer;
                    		} else {
                    		    $answer = $answers[$answerText];
                    		}
                    		$question->setCorrectAnswer($answer);

                    		$cellIterator->next();
                    		$answer = null;
                    	    $answerText = $cellIterator->current()->getValue();
                    		if (!isset($answers[$answerText])) {
                                $answer = new Answer();
                                $answer->setText($answerText);
                                $em->persist($answer);
                                $answers[$answerText] = $answer;
                    		} else {
                    		    $answer = $answers[$answerText];
                    		}
                    		$question->setWrongAnswer1($answer);

                    		$cellIterator->next();
                    		$answer = null;
                    	    $answerText = $cellIterator->current()->getValue();
                    		if (!isset($answers[$answerText])) {
                                $answer = new Answer();
                                $answer->setText($answerText);
                                $em->persist($answer);
                                $answers[$answerText] = $answer;
                    		} else {
                    		    $answer = $answers[$answerText];
                    		}
                    		$question->setWrongAnswer2($answer);

                    		$em->persist($question);

                    		++$rowNum;
                        }

                        $em->flush();
                    }
                }
            }
        }

        $questionsHref = $questionFile->getWebPathForStoredFileIfExists();

        return array('form' => $questionUploadForm->createView(), 'questionsHref' => $questionsHref, 'errors' => $errors, 'rowNum' => $rowNum);
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

        foreach ($rowIterator as $row) {
            $rowNum = $rowIterator->key();
            if (1 === $rowNum) {
                continue;
            }
    		$cellIterator = $row->getCellIterator();
    		$cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $i => $cell) {
                if ($i >= $validHeaderOrigSize && $cell->getValue()) {
                    $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban a megengedettnél ('.$validHeaderOrigSize.') több mező van kitöltve (' . ($i+1) . '. cella)!';
                    break;
                }

                if ($i < $validHeaderOrigSize && !$cell->getValue()) {
                    $errors['QUESTIONS'][] = 'A(z) ' . $rowNum  . '. sorban a(z) ' . ($i+1) . '. cella nincs kitöltve!';
                }
            }
        }

        return $errors;
    }

    /**
     * @Route("/quiz/{rateableId}")
     * @Template()
     */
    public function indexAction($rateableId) {
        $doctrine = $this->getDoctrine();
        $rateable = $doctrine
            ->getRepository('AcmeRatingBundle:Rateable')
            ->find($rateableId);

        $questionRepo = $doctrine
            ->getRepository('AcmeQuizBundle:Question');

        $questions = $questionRepo->find3RandomQuestionsNotShownInTheLast2Weeks($rateableId);

        if (count($questions) === 3) {
            $questionRepo->logQuestionsOccured($questions);
        }

        $questionsWithAnswers = $this->createQuestionsWithAnswersArray($questions);

        return array('name' => $rateableId, 'questions' => $questionsWithAnswers);
    }

    /**
     * @Route("/quiz/questionnaire")
     * @Template()
     */
    public function questionnaireAction() {
        $doctrine = $this->getDoctrine();
        $questionRepo = $doctrine
            ->getRepository('AcmeQuizBundle:Question');

        $questions = $questionRepo->findAll();

        $questionFile = new QuestionFile();
        $questionsHref = $questionFile->getWebPathForStoredFileIfExists();

        return array('questions' => $questions, 'questionsHref' => $questionsHref);
    }

    private function createQuestionsWithAnswersArray($questions) {
        $questionsWithAnswers = array();

        foreach ($questions as  $question) {
            $questionId = $question->getId();
            $questionsWithAnswers[$questionId]['QUESTION'] = $question->getText();
            $correctAnswer = $question->getCorrectAnswer();
            $questionsWithAnswers[$questionId]['CORRECT_ANSWER'] = $correctAnswer->getText();
            $questionsWithAnswers[$questionId]['CORRECT_ANSWER_ID'] = $correctAnswer->getId();
            $wrongAnswer1 = $question->getWrongAnswer1();
            $questionsWithAnswers[$questionId]['WRONG_ANSWER1'] = $wrongAnswer1->getText();
            $questionsWithAnswers[$questionId]['WRONG_ANSWER1_ID'] = $wrongAnswer1->getId();
            $wrongAnswer2 = $question->getWrongAnswer2();
            $questionsWithAnswers[$questionId]['WRONG_ANSWER2'] = $wrongAnswer2->getText();
            $questionsWithAnswers[$questionId]['WRONG_ANSWER2_ID'] = $wrongAnswer2->getId();
        }

        return $questionsWithAnswers;
    }

    private function craeteTestQuizData() {
        $rateable = $this->getDoctrine()
            ->getRepository('AcmeRatingBundle:Rateable')
            ->find($rateableId);

        $answer = new Answer();
        $answer->setText('Első kérdés');

        $question = new Question();
        $question->setText('Első kérdés');
        $question->setCorrectAnswer($answer);
        $question->setWrongAnswer1($answer);
        $question->setWrongAnswer2($answer);

        $quiz = new Quiz();
        $quiz->setRateable($rateable);
        $quiz->setQuestion($question);
        $quiz->setGivenAnswer($answer);

        $em = $this->getDoctrine()->getManager();
        $em->persist($answer);
        $em->persist($question);
        $em->persist($quiz);
        $em->flush();
    }
}
