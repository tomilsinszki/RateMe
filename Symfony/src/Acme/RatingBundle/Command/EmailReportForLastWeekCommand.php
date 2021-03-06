<?php

namespace Acme\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmailReportForLastWeekCommand extends ContainerAwareCommand {    
    
    private $managerStatement           = null;
    private $managersDataToSendEmailsTo = null;
    
    protected function configure() {
        $this->setName('email:reporting_for_managers');
        $this->setDescription('Sending report for managers(with attached excel link)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->loadManagersDataToSendEmailsTo();
        $this->sendEmailsToManagers();
    }
    
    private function sendEmailsToManagers() {
        $startDateTime = new \DateTime(date('Y-m-d', strtotime('first day of last month')));
        $endDateTime = new \DateTime(date('Y-m-d', strtotime('last day of last month')));
        $endDateTime->setTime(23, 59, 59);
        foreach($this->managersDataToSendEmailsTo as $manager) {
            if(NULL != $manager['rateableCollectionId']) {
                $this->sendEmailToManager($manager, $startDateTime, $endDateTime);
            }
        }
    }
    
    private function sendEmailToManager($manager, $startDateTime, $endDateTime) {
        $translator = $this->getContainer()->get('translator');
        $message    = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject($translator->trans('emailTitle', array(), 'riport') . " - {$manager['rateableCollectionName']}");
        $message->setFrom(array('info@rateme.hu' => $translator->trans('emailFrom', array(), 'riport')));
        $message->setTo($manager['emailAddress']);
        $message->addBcc('rateme.archive@gmail.com');

        $ratingCountAndAvg = $this->getRatingCountAndAvg($manager, $startDateTime, $endDateTime);
        $mostFiveRateForRateable = $this->getMostFiveRate($manager, $startDateTime, $endDateTime);
        $mostQuizCorrectAnswerByRateable = $this->getMostQuizCorrectAnswerByRateable($manager, $startDateTime, $endDateTime);
        $leastAmountContactsFixedByRateable = $this->getLeastAmountContactsFixedByRateable($manager, $startDateTime, $endDateTime);
        $worstRatedRateable = $this->getWorstRatedRateable($manager, $startDateTime, $endDateTime);
        $embeddedImages = $this->embedImagesIntoMessage(
            $mostFiveRateForRateable, 
            $mostQuizCorrectAnswerByRateable, 
            $leastAmountContactsFixedByRateable, 
            $worstRatedRateable, 
            $message
        );  

        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:RateableCollection:reportForLastWeekEmail.html.twig', 
            array(
                'manager'                       => $manager, 
                'images'                        => $embeddedImages,
                'excelRiportUrl'                => $this->getExcelReportUrl($manager, $startDateTime, $endDateTime),
                'ratingCount'                   => $ratingCountAndAvg['count'],
                'ratingAvg'                     => round($ratingCountAndAvg['ratingAvg']),
                'quizAvgResult'                 => $this->getQuizAvgResult($manager, $startDateTime, $endDateTime),
                'mostFiveRateName'              => (null != $mostFiveRateForRateable) ? $mostFiveRateForRateable['rateableName'] : null,
                'mostFiveRateCount'             => (null != $mostFiveRateForRateable) ? $mostFiveRateForRateable['rateableRateCount'] : null,
                'mostQuizCorrectAnswerName'     => (null != $mostQuizCorrectAnswerByRateable) ? $mostQuizCorrectAnswerByRateable['rateableName'] : null,
                'mostQuizCorrectAnswerPercent'  => (null != $mostQuizCorrectAnswerByRateable) ? round($mostQuizCorrectAnswerByRateable['quizReplyRatio'] * 100) : null,
                'leastAmountContactsFixedName'  => (null != $leastAmountContactsFixedByRateable) ? $leastAmountContactsFixedByRateable['rateableName'] : null,
                'leastAmountContactsFixedCount' => (null != $leastAmountContactsFixedByRateable) ? $leastAmountContactsFixedByRateable['contactFixedCount'] : null,
                'worstRatedRateableName'        => (null != $worstRatedRateable) ? $worstRatedRateable['rateableName'] : null,
                'worstRatedRateableAvg'         => (null != $worstRatedRateable) ? round($worstRatedRateable['rateableRateAvg'], 2) : null,
            )
        ));        
        $this->getContainer()->get('mailer')->send($message);
    }
    
    private function getWorstRatedRateable($manager, $startDateTime, $endDateTime) {
        $connection = $this->getContainer()->get('database_connection');

        $query = "
            SELECT
                rateable.name AS rateableName,
                AVG(rating.stars) AS rateableRateAvg,
                image.id AS imageFileName,
                image.path AS imageFileExtension
            FROM rateable
            LEFT JOIN rating ON rating.rateable_id = rateable.id
                AND '{$startDateTime->format('Y-m-d H:i:s')}' <= rating.created
                AND rating.created <= '{$endDateTime->format('Y-m-d H:i:s')}'
            LEFT JOIN image ON image.id=rateable.image_id
            WHERE
                rateable.is_active=1 AND
                rateable.collection_id={$manager['rateableCollectionId']}
            GROUP BY rateable.id
            ORDER BY rateableRateAvg ASC, rateableName ASC
        ";
        
        $worstRatedRateableStatement = $connection->executeQuery($query);
        $worstRatedRateableStatement->execute();
        $worstRatedRateable = $worstRatedRateableStatement->fetchAll();        
        return (empty($worstRatedRateable)) ? null : $worstRatedRateable[0];
    }
    
    private function getLeastAmountContactsFixedByRateable($manager, $startDateTime, $endDateTime) {
        $connection = $this->getContainer()->get('database_connection');

        $contactCountQuery = "
            SELECT COUNT(contact.id) AS contact_count
            FROM contact
            LEFT JOIN rateable ON contact.rateable_id=rateable.id
            WHERE
                rateable.is_active=1 AND
                rateable.collection_id={$manager['rateableCollectionId']} AND
                '{$startDateTime->format('Y-m-d H:i:s')}' <= contact.contact_happened_at AND
                contact.contact_happened_at <= '{$endDateTime->format('Y-m-d H:i:s')}'
        ";
        $contactCountStatement = $connection->executeQuery($contactCountQuery);
        $contactCountStatement->execute();
        $contactCount = $contactCountStatement->fetchColumn();

        if (empty($contactCount)) {
            return null;
        }

        $leastAmountContactsFixedQuery = "
            SELECT
                rateable.name AS rateableName,
                COUNT(contact.id) AS contactFixedCount,
                image.id AS imageFileName,
                image.path AS imageFileExtension
            FROM rateable
            LEFT JOIN contact ON contact.rateable_id = rateable.id
                AND '{$startDateTime->format('Y-m-d H:i:s')}' <= contact.contact_happened_at
                AND contact.contact_happened_at <= '{$endDateTime->format('Y-m-d H:i:s')}'
            LEFT JOIN image ON image.id=rateable.image_id
            WHERE
                rateable.is_active=1 AND
                rateable.collection_id={$manager['rateableCollectionId']}
            GROUP BY rateable.id
            ORDER BY contactFixedCount ASC, rateableName ASC
        ";
        $leastAmountContactsFixedStatement = $connection->executeQuery($leastAmountContactsFixedQuery);
        $leastAmountContactsFixedStatement->execute();
        $leastAmountContactsFixed = $leastAmountContactsFixedStatement->fetchAll();        
        return (empty($leastAmountContactsFixed)) ? null : $leastAmountContactsFixed[0]; 
    }
    
    private function getMostQuizCorrectAnswerByRateable($manager, $startDateTime, $endDateTime) {
        $connection = $this->getContainer()->get('database_connection');

        $query = "
            SELECT
                ra.name AS rateableName,
                count(qr.id) AS allQuizReplyCount, 
                sum(CASE WHEN qr.wrong_given_answer_id IS NULL THEN 1 ELSE 0 END) AS correctQuizReplyCount,
                sum(CASE WHEN qr.wrong_given_answer_id IS NOT NULL THEN 1 ELSE 0 END) AS wrongQuizReplyCount,
                sum(CASE WHEN qr.wrong_given_answer_id IS NULL THEN 1 ELSE 0 END)/count(qr.id) AS quizReplyRatio,
                i.id AS imageFileName,
                i.path AS imageFileExtension
            FROM rateable ra                                                            
            LEFT JOIN quiz q ON q.rateable_id=ra.id
            LEFT JOIN quiz_reply qr ON qr.quiz_id=q.id
            LEFT JOIN quiz_question qq ON qq.id=qr.question_id
            LEFT JOIN image i ON i.id=ra.image_id
            WHERE
                '{$startDateTime->format('Y-m-d H:i:s')}' <= q.created AND
                q.created <= '{$endDateTime->format('Y-m-d H:i:s')}' AND
                ra.collection_id = {$manager['rateableCollectionId']}
            GROUP BY ra.name                                                            
            ORDER BY (SUM(CASE WHEN qr.wrong_given_answer_id IS NULL THEN 1 ELSE 0 END)/count(qr.id)) DESC, count(qr.id) DESC, q.elapsed_seconds ASC
        ";

        $mostQuizCorrectAnswerStatement = $connection->executeQuery($query);
        $mostQuizCorrectAnswerStatement->execute();
        $mostQuizCorrectAnswer = $mostQuizCorrectAnswerStatement->fetchAll();        
        return (empty($mostQuizCorrectAnswer)) ? null : $mostQuizCorrectAnswer[0];
    }
    
    private function getMostFiveRate($manager, $startDateTime, $endDateTime) {
        $connection = $this->getContainer()->get('database_connection');
        
        $query = "
            SELECT
                ra.name AS rateableName,
                count(r.stars) AS rateableRateCount,
                i.id AS imageFileName,
                i.path AS imageFileExtension
            FROM rateable ra                                                            
            LEFT JOIN rating r ON r.rateable_id=ra.id
            LEFT JOIN image i ON i.id=ra.image_id                                                           
            WHERE r.created >= '{$startDateTime->format('Y-m-d H:i:s')}'
                AND r.created <= '{$endDateTime->format('Y-m-d H:i:s')}'
                AND r.stars = 5                                                               
                AND ra.collection_id = {$manager['rateableCollectionId']}
            GROUP BY ra.name
            ORDER BY count(r.stars) DESC
        ";

        $mostFiveRateStatement = $connection->executeQuery($query);
        $mostFiveRateStatement->execute();
        $mostFiveRate = $mostFiveRateStatement->fetchAll();
        return (empty($mostFiveRate)) ? null : $mostFiveRate[0];        
    }
    
    private function getQuizAvgResult($manager, $startDateTime, $endDateTime) {
        $connection             = $this->getContainer()->get('database_connection');

        $allReplyQuery = "
            SELECT
                count(*) AS count
            FROM quiz_reply qr
            LEFT JOIN quiz_question qq ON qq.id=qr.question_id
            LEFT JOIN quiz q ON q.id=qr.quiz_id
            WHERE
                q.created >= '{$startDateTime->format('Y-m-d H:i:s')}'
                AND q.created <= '{$endDateTime->format('Y-m-d H:i:s')}'
                AND qq.rateable_collection_id = {$manager['rateableCollectionId']}
        ";
        $allReplyCountStatement = $connection->executeQuery($allReplyQuery);
        $allReplyCountStatement->execute();
        $allReplyCount = $allReplyCountStatement->fetchAll();

        $correctReplyCountQuery = "
            SELECT
                count(*) AS count
            FROM quiz_reply qr
            LEFT JOIN quiz_question qq ON qq.id=qr.question_id
            LEFT JOIN quiz q ON q.id=qr.quiz_id
            WHERE q.created >= '{$startDateTime->format('Y-m-d H:i:s')}'
                AND q.created <= '{$endDateTime->format('Y-m-d H:i:s')}'
                AND qr.wrong_given_answer_id IS NULL 
                AND qq.rateable_collection_id = {$manager['rateableCollectionId']}
        ";
        $correctReplyCountStatement = $connection->executeQuery($correctReplyCountQuery);
        $correctReplyCountStatement->execute();
        $correctReplyCount = $correctReplyCountStatement->fetchAll();

        if(0 == $allReplyCount[0]['count']) {
            return 0;
        }
        return round(($correctReplyCount[0]['count']/$allReplyCount[0]['count'])*100);
    }
    
    private function getRatingCountAndAvg($manager, $startDateTime, $endDateTime) {
        $connection = $this->getContainer()->get('database_connection');

        $query = "
            SELECT
                count(*) AS count,
                avg(r.stars) AS ratingAvg
            FROM rating r                                                            
            LEFT JOIN rateable ra ON ra.id=r.rateable_id
            LEFT JOIN rateable_collection rc ON rc.id=ra.collection_id
            WHERE r.created >= '{$startDateTime->format('Y-m-d H:i:s')}'
              AND r.created <= '{$endDateTime->format('Y-m-d H:i:s')}'
              AND rc.id = {$manager['rateableCollectionId']}
        ";

        $ratingCountStatement = $connection->executeQuery($query);
        $ratingCountStatement->execute();
        $ratingCount = $ratingCountStatement->fetchAll();
        return (empty($ratingCount)) ? null : $ratingCount[0];         
    }
    
    private function getExcelReportUrl($manager, $startDateTime, $endDateTime) {
        return 'http://rateme.hu' . 
              $this->getContainer()->get('router')->generate('report_download', 
                    array(
                        'rateableCollectionId' => $manager['rateableCollectionId'],
                        'startDateTime'        => $startDateTime->format('Y-m-d_H-i-s'),
                        'endDateTime'          => $endDateTime->format('Y-m-d_H-i-s'),
                    )
              );
    }
    
    private function embedImagesIntoMessage($mostFiveRateForRateable, $mostQuizCorrectAnswerByRateable, $leastAmountContactsFixedByRateable, $worstRatedRateable, $message) {
        $webRootPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web';
        $images = array();
        
        if(null != $mostFiveRateForRateable) { 
            $images['mostFiveRateForRateable'] = $message->embed(\Swift_Image::fromPath("{$webRootPath}/uploads/images/{$mostFiveRateForRateable['imageFileName']}.{$mostFiveRateForRateable['imageFileExtension']}"));                 
        }
        if(null != $mostQuizCorrectAnswerByRateable) { 
            $images['mostQuizCorrectAnswerByRateable'] = $message->embed(\Swift_Image::fromPath("{$webRootPath}/uploads/images/{$mostQuizCorrectAnswerByRateable['imageFileName']}.{$mostQuizCorrectAnswerByRateable['imageFileExtension']}"));                 
        }
        if(null != $leastAmountContactsFixedByRateable) { 
            $images['leastAmountContactsFixedByRateable'] = $message->embed(\Swift_Image::fromPath("{$webRootPath}/uploads/images/{$leastAmountContactsFixedByRateable['imageFileName']}.{$leastAmountContactsFixedByRateable['imageFileExtension']}"));                 
        }
        if(null != $worstRatedRateable) { 
            $images['worstRatedRateable'] = $message->embed(\Swift_Image::fromPath("{$webRootPath}/uploads/images/{$worstRatedRateable['imageFileName']}.{$worstRatedRateable['imageFileExtension']}"));                 
        }
        $images['logo'] = $message->embed(\Swift_Image::fromPath("{$webRootPath}/images/emailLogo.png"));        
            
        return $images;
    }
    
    private function loadManagersDataToSendEmailsTo() {        
        $connection = $this->getContainer()->get('database_connection');

        $query = "
            SELECT
                u.id AS userId,
                u.username AS userName,
                u.last_name AS lastName,
                u.first_name AS firstName,
                u.email_address AS emailAddress,                                                                
                rc.id AS rateableCollectionId,
                rc.name AS rateableCollectionName
            FROM user u
            LEFT JOIN user_group ug ON ug.user_id=u.id
            LEFT JOIN role r ON r.id=ug.group_id
            LEFT JOIN rateable_collection_owner rco ON rco.user_id=u.id
            LEFT JOIN rateable_collection rc ON rc.id=rco.collection_id
            WHERE
                r.role = 'ROLE_MANAGER' AND
                u.is_active = 1 AND
                u.email_address IS NOT NULL AND
                u.email_address<>''
        ";

        $this->managerStatement = $connection->executeQuery($query);
        $this->managerStatement->execute();
        $this->managersDataToSendEmailsTo = $this->managerStatement->fetchAll();
    }
    
}

?>
