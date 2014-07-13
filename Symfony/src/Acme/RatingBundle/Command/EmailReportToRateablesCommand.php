<?php

namespace Acme\RatingBundle\Command;

use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailReportToRateablesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('email:report_to_rateables')
            ->setDescription('User weekly result sender script')
            ->addArgument('from', InputArgument::REQUIRED, 'Date from')
            ->addArgument('to', InputArgument::REQUIRED, 'Date to')
        ;
    }

    protected function getEnabledModules() {
        return array(
            'contacts' => true,
            'ratings' => true,
            'quiz' => true,
            'subrating' => true,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new \DateTime($input->getArgument('from') . ' 00:00:00');
        $to = new \DateTime($input->getArgument('to') . ' 23:59:59');

        $modules = $this->getEnabledModules();
        $data = $this->loadData($from, $to);
        $rateables = $this->getRateables();

        if ($modules['subrating']) {
            $subratings = $this->getSubratings($from, $to);
        }

        foreach($rateables as $rateable) {
            $rateableId = $rateable->getId();
            $rateableData = array_map(function ($item) use ($rateableId) {
                return $item[$rateableId];
            }, $data);

            if ($modules['subrating']) {
                $rateableData['subrating'] = array_filter($subratings, function ($item) use ($rateableId) {
                    return $item['id'] === $rateableId;
                });
                $rateableData['subratingSums'] = $this->calculateSubratingQuestionsSums($rateableData['subrating']);
            }

            $this->addEmailToQueue($rateable, $rateableData);
        }
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

    private function getEm() {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    private function getRateables() {
        $query = $this->getEm()->createQueryBuilder()
            ->select('u, r')
            ->from('AcmeRatingBundle:Rateable', 'r', 'r.id')
            ->join('r.rateableUser', 'u')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
        ;
        return $query->getQuery()->getResult();
    }

    private function loadData(\DateTime $from, \DateTime $to) {
        $modules = $this->getEnabledModules();
        $rateables = array();

        if ($modules['contacts']) {
            $rateables['contacts'] = $this->getContacts($from, $to);
        }
        if ($modules['ratings']) {
            $rateables['ratings'] = $this->getRatings($from, $to);
        }
        if ($modules['quiz']) {
            $rateables['quiz'] = array_replace_recursive(
                $this->getQuizes($from, $to),
                $this->getQuizWrongAnswers($from, $to)
            );
        }

        return $rateables;
    }

    private function getContacts(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('COUNT(c) AS contacts, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r', 'r.id')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.contacts', 'c', Join::WITH, 'c.contactHappenedAt >= :from AND c.contactHappenedAt <= :to')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->groupBy('r.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function getRatings(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('COUNT(rat) AS ratings, AVG(rat.stars) AS ratings_average, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r', 'r.id')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.ratings', 'rat', Join::WITH, 'rat.created >= :from AND rat.created <= :to')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->groupBy('r.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function getQuizes(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('3 * COUNT(q) AS quizzes, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r', 'r.id')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.quizzes', 'q', Join::WITH, 'q.created >= :from AND q.created <= :to')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->groupBy('r.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function getQuizWrongAnswers(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('COUNT(qr.wrongGivenAnswer) AS wrong_answers, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r', 'r.id')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.quizzes', 'q', Join::WITH, 'q.created >= :from AND q.created <= :to')
            ->leftJoin('q.quizReplies', 'qr')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->groupBy('r.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function getSubratings(\DateTime $from, \DateTime $to) {
        $query = $this->getEm()->createQueryBuilder()
            ->select('q.id AS question_id, q.text AS question, t.name AS answer, COUNT(s.id) AS answers, r.id')
            ->from('AcmeRatingBundle:Rateable', 'r')
            ->join('r.rateableUser', 'u')
            ->leftJoin('r.ratings', 'rat', Join::WITH, 'rat.created >= :from AND rat.created <= :to')
            ->leftJoin('rat.subRatings', 's')
            ->leftJoin('s.answer', 'a')
            ->leftJoin('a.answerType', 't')
            ->leftJoin('a.question', 'q')
            ->where('u.isActive = 1')
            ->andWhere('u.email IS NOT NULL')
            ->andWhere('q.text IS NOT NULL')
            ->andWhere('t.name IS NOT NULL')
            ->groupBy('r.id, q.id, t.id')
            ->setParameters(array(
                'from' => $from->format('Y-m-d H:i:m'),
                'to' => $to->format('Y-m-d H:i:m'),
            ))
        ;
        return $query->getQuery()->getResult();
    }

    private function addEmailToQueue($rateable, $rateableData) {
        $message = \Swift_Message::newInstance();
        $message->setCharset('UTF-8');
        $message->setContentType('text/html');
        $message->setSubject('Dolgozó heti értékelés');
        $message->setFrom(array('dontreply@rate.me.uk' => 'RateMe'));
        $message->setTo($rateable->getRateableUser()->getEmail());
        $message->addBcc('rateme.archive@gmail.com');
        $embeddedImages = $this->embedImagesIntoMessage($message);
        $message->setBody($this->getContainer()->get('templating')->render(
            'AcmeRatingBundle:Rateable:weeklyRatingEmail.html.twig',
            $rateableData + array(
                'images' => $embeddedImages,
                'name' => $rateable->getName(),
            )
        ), 'text/html');
        $this->getContainer()->get('mailer')->send($message);
    }

    private function embedImagesIntoMessage($message) {
        $webRootPath = $this->getContainer()->get('kernel')->getRootDir()."/../web";

        $images = array(
            'background' => $message->embed(\Swift_Image::fromPath("$webRootPath/images/repeater_bg.png")),
            'logo' => $message->embed(\Swift_Image::fromPath("$webRootPath/images/emailLogo.png")),
        );

        return $images;
    }

}
