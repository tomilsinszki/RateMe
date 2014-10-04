<?php

namespace Acme\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query\Expr\Join;

class EmailReportToRateablesCommand extends ContainerAwareCommand
{

    private $rateablesDataQueryText =<<<EOD
SELECT
    username,
    email,
    name,
    id AS rateable_id,
    SUM(contacts) AS contacts,
    SUM(ratings) AS ratings,
    SUM(ratings_average) AS ratings_average,
    SUM(quizzes) AS quizzes,
    SUM(wrong_answers) AS wrong_answers
FROM
    (
        SELECT
           `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            rateable.id AS id,
            COUNT(contact.id) AS contacts,
            NULL AS ratings,
            NULL AS ratings_average,
            NULL AS quizzes,
            NULL AS wrong_answers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN contact
            ON contact.rateable_id = rateable.id AND
               contact.contact_happened_at >= '{{ from }}' AND contact.contact_happened_at <= '{{ to }}'
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL AND rateable.is_active = 1
        GROUP BY rateable.id
    UNION
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            rateable.id AS id,
            NULL AS contacts,
            COUNT(rating.id) AS ratings,
            AVG(rating.stars) AS ratings_average,
            NULL AS quizzes,
            NULL AS wrong_answers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN rating
            ON rating.rateable_id = rateable.id AND rating.created >= '{{ from }}' AND rating.created <= '{{ to }}'
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL AND rateable.is_active = 1
        GROUP BY rateable.id
    UNION
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            rateable.id AS id,
            NULL AS contacts,
            NULL AS ratings,
            NULL AS ratings_average,
            COUNT(quiz_reply.id) AS quizzes,
            NULL AS wrong_answers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN quiz
            ON quiz.rateable_id = rateable.id AND quiz.created >= '{{ from }}' AND quiz.created <= '{{ to }}'
        LEFT JOIN quiz_reply
            ON quiz_reply.quiz_id = quiz.id
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL AND rateable.is_active = 1
        GROUP BY rateable.id
    UNION
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            rateable.id AS id,
            NULL AS contacts,
            NULL AS ratings,
            NULL AS ratings_average,
            NULL AS quizzes,
            COUNT(quiz_reply.wrong_given_answer_id) AS wrong_answers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN quiz
            ON quiz.rateable_id = rateable.id AND quiz.created >= '{{ from }}' AND quiz.created <= '{{ to }}'
        LEFT JOIN quiz_reply
            ON quiz_reply.quiz_id = quiz.id
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL AND rateable.is_active = 1 AND quiz_reply.wrong_given_answer_id IS NOT NULL
        GROUP BY rateable.id
    ) AS tbl
GROUP BY id
EOD;

    private function getEm() {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function configure()
    {
        $this
            ->setName('email:report_to_rateables')
            ->setDescription('User weekly result sender script')
            ->addArgument('from', InputArgument::REQUIRED, 'Date from')
            ->addArgument('to', InputArgument::REQUIRED, 'Date to')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new \DateTime($input->getArgument('from') . ' 00:00:00');
        $to = new \DateTime($input->getArgument('to') . ' 23:59:59');

        $rateables = $this->loadData($from, $to);
        $subratings = $this->getSubratings($from, $to);
        foreach($rateables AS $rateable) {
            $rateableSubratings = $this->filterRateableSubratings($rateable, $subratings);
            $this->processRateable($rateable, $rateableSubratings);
        }
    }

    private function filterRateableSubratings($rateable, $subratings) {
        $rateableId = $rateable['rateable_id'];
        $filteredSubratings = array_filter($subratings, function ($item) use ($rateableId) {
            return $item['id'] == $rateableId;
        });
        return array_values($filteredSubratings);
    }

    private function processRateable($rateable, $subratings) {
        if ($this->hasEmailAnyField($rateable, $subratings)) {
            $this->addEmailToQueue($rateable, $subratings);
        }
    }

    private function hasEmailAnyField($rateable, $subratings) {
        return $rateable['contacts'] || $rateable['ratings'] || $rateable['quizzes'] || !empty($subratings);
    }

    private function loadData(\DateTime $from, \DateTime $to) {
        $connection = $this->getContainer()->get('database_connection');
        $query = $this->getPreparedQuery($from, $to);
        $statement = $connection->executeQuery($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function getPreparedQuery(\DateTime $from, \DateTime $to) {
        $query = $this->rateablesDataQueryText;
        $query = str_replace(
            array('{{ from }}', '{{ to }}'),
            array($from->format('Y-m-d H:i:m'), $to->format('Y-m-d H:i:m')),
            $query
        );
        return $query;
    }

    private function getSubratings(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('q.id AS question_id, q.text AS question,' .
                "CASE WHEN a.text IS NULL OR a.text = '' THEN t.name ELSE a.text END AS answer," .
                'COUNT(s.id) AS answers, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.ratings', 'rat', Join::WITH, 'rat.created >= :from AND rat.created <= :to')
            ->leftJoin('rat.subRatings', 's')
            ->leftJoin('s.answer', 'a')
            ->leftJoin('a.answerType', 't')
            ->leftJoin('a.question', 'q')
            ->where('u.isActive = 1')
            ->andWhere('r.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->andWhere('q.text IS NOT NULL')
            ->andWhere('t.name IS NOT NULL')
            ->groupBy('r, q, t')
            ->orderBy('r.id, q.id, a.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function addEmailToQueue($rateableData, $subratingData) {
        $message = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject('Dolgozó heti értékelés');
        $message->setFrom(array('info@rateme.hu' => 'RateMe'));
        $message->setTo($rateableData['email']);
        $message->addBcc('rateme.archive@gmail.com');
        $embeddedImages = $this->embedImagesIntoMessage($message, $rateableData['ratings_average']);
        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:Rateable:rateableReportEmail.html.twig',
            $rateableData
                + array('images' => $embeddedImages)
                + array(
                    'subratings' => $subratingData,
                    'subratingSums' => $this->calculateSubratingQuestionsSums($subratingData)
                )
        ), 'text/html');
        $this->getContainer()->get('mailer')->send($message);
    }

    private function embedImagesIntoMessage($message, $rating) {
        $webRootPath = $this->getContainer()->get('kernel')->getRootDir()."/../web";

        $images = array(
            'background' => $message->embed(\Swift_Image::fromPath("$webRootPath/images/repeater_bg.png")),
            'logo' => $message->embed(\Swift_Image::fromPath("$webRootPath/images/emailLogo.png")),
            'star_10' => $message->embed(\Swift_Image::fromPath("$webRootPath/images/half_stars/star_10.png")),
        );

        if ($rating <= 4) {
            $images['star_0'] = $message->embed(\Swift_Image::fromPath("$webRootPath/images/half_stars/star_0.png"));
        }

        $part = (round($rating, 1) * 10) % 10;
        if ($part !== 0) {
            $images["star_$part"]
                = $message->embed(\Swift_Image::fromPath("$webRootPath/images/half_stars/star_$part.png"));
        }

        return $images;
    }

    private function calculateSubratingQuestionsSums($subratings) {
        $sums = array();

        foreach ($subratings as $rating) {
            $sums[$rating['question_id']] = 0;
        }
        foreach ($subratings as $rating) {
            $sums[$rating['question_id']] += $rating['answers'];
        }

        return $sums;
    }

}
