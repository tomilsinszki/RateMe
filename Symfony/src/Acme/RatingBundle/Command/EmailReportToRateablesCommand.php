<?php

namespace Acme\RatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class EmailReportToRateablesCommand extends ContainerAwareCommand
{

    private $rateablesDataQueryText =<<<EOD
SELECT
    username,
    email,
    name,
    SUM(ratings) AS ratings,
    SUM(ratings_average) AS ratings_average,
    3 * SUM(quizes) AS quizes,
    SUM(wrong_asnwers) AS wrong_asnwers
FROM
    (
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            COUNT(rating.id) AS ratings,
            AVG(rating.stars) AS ratings_average,
            NULL AS quizes,
            NULL AS wrong_asnwers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN rating
            ON rating.rateable_id = rateable.id AND rating.created >= '{{ from }}' AND rating.created <= '{{ to }}'
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL
        GROUP BY `user`.username
    UNION
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            NULL AS ratings,
            NULL AS ratings_average,
            COUNT(quiz.id) AS quizes,
            NULL AS wrong_asnwers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN quiz
            ON quiz.rateable_id = rateable.id AND quiz.created >= '{{ from }}' AND quiz.created <= '{{ to }}'
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL
        GROUP BY `user`.username
    UNION
        SELECT
            `user`.username,
            `user`.email_address AS email,
            rateable.name AS name,
            NULL AS ratings,
            NULL AS ratings_average,
            NULL AS quizes,
            COUNT(quiz_reply.wrong_given_answer_id) AS wrong_asnwers
        FROM rateable
        JOIN `user`
            ON `user`.id = rateable.rateable_user_id
        LEFT JOIN quiz
            ON quiz.rateable_id = rateable.id AND quiz.created >= '{{ from }}' AND quiz.created <= '{{ to }}'
        LEFT JOIN quiz_reply
            ON quiz_reply.quiz_id = quiz.id
        WHERE
            `user`.is_active = 1 AND `user`.email_address IS NOT NULL
        GROUP BY `user`.username
    ) AS tbl
GROUP BY username
EOD;

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
        foreach($rateables AS $rateable) {
            $this->addEmailToQueue($rateable);
        }
    }

    private function loadData(\DateTime $from, \DateTime $to) {
        $connection = $this->getContainer()->get('database_connection');
        $query = $this->rateablesDataQueryText;
        $query = str_replace(
            array('{{ from }}', '{{ to }}'),
            array($from->format('Y-m-d H:i:m'), $to->format('Y-m-d H:i:m')),
            $query
        );
        $statement = $connection->executeQuery($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function addEmailToQueue($rateableData) {
        $message = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject('Dolgozó heti értékelés');
        $message->setFrom(array('dontreply@rate.me.uk' => 'RateMe'));
        $message->setTo($rateableData['email']);
        $message->addBcc('rateme.archive@gmail.com');
        $embeddedImages = $this->embedImagesIntoMessage($message, $rateableData['ratings_average']);
        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:Rateable:weeklyRatingEmail.html.twig',
            $rateableData + array('images' => $embeddedImages)
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

        $part = floor(($rating * 10) % 10);
        if ($part !== 0) {
            $images["star_$part"]
                = $message->embed(\Swift_Image::fromPath("$webRootPath/images/half_stars/star_$part.png"));
        }

        return $images;
    }

}
