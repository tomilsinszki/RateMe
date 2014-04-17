<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acme\RatingBundle\Entity\Rateable;
use Acme\RatingBundle\Entity\Image;
use Acme\RatingBundle\Utility\Validator;
use Acme\UserBundle\Utility\CurrentUser;

class RateableCollectionController extends Controller
{
    private $getRateablesForCollectionQueryText =
        'SELECT 
            r.id AS rateableId,
            r.name AS rateableName, 
            i.id AS imageFileName,
            i.path AS imageFileExtension
        FROM rateable r 
        LEFT JOIN image i ON r.image_id=i.id 
        WHERE 
            r.is_active=1 AND 
            r.collection_id=%1$d';
    private $getRateablesForCollectionStatement = null;
    
    private $getContactsForCollectionQueryText =
        'SELECT
            rb.id AS rateableId,
            c.contact_happened_at AS contactHappenedAt
        FROM contact c
        LEFT JOIN rateable rb ON c.rateable_id=rb.id
        WHERE 
            rb.collection_id=%1$d AND
            rb.is_active=1 AND 
            c.contact_happened_at BETWEEN "%2$s" AND "%3$s"
        ORDER BY c.contact_happened_at';
    private $getContactsForCollectionStatement = null;

    private $getRatingsAverageForLastTwelveMonthsQueryText =
        'SELECT 
            AVG(stars) AS rating_average 
        FROM rating
        WHERE updated BETWEEN "%1$s" AND "%2$s"';
    private $getRatingsAverageForLastTwelveMonthsStatement = null;

    private $getRatingsForCollectionQueryText =
        'SELECT 
            rb.id AS rateableId, 
            r.stars AS stars,
            r.updated AS ratingReceivedAt
        FROM rateable_collection rc 
        LEFT JOIN rateable rb ON rc.id=rb.collection_id 
        LEFT JOIN rating r ON rb.id=r.rateable_id 
        WHERE 
            r.stars IS NOT NULL AND 
            rb.is_active=1 AND 
            rc.id=%1$d AND
            r.updated BETWEEN "%2$s" AND "%3$s"
        ORDER BY r.updated;';
    private $getRatingsForCollectionStatement = null;
    
    private $reportCurrentPeriod = null;
    private $reportPreviousPeriod = null;
    private $reportCollection = null;
    private $contactCountByDayChartData = null;
    private $ratingCountByDayChartData = null;
    private $ratingAvgByDayChartData = null;
    private $rateableReportsData = array();
    private $rateableReportsDataSortedByRatingAverage = array();
    private $rateableReportsDataSortedByRatingCount = array();
    private $rateableAveragesChartData = array();
    private $rateableRatingCountsChartData = array();
    private $overallRatingAverageByMonthChartData = array();
    private $overallContactsCount = array('currentPeriod' => 0, 'previousPeriod' => 0);
    private $overallRatingsCount = array('currentPeriod' => 0, 'previousPeriod' => 0);
    private $overallRatingsSum = array('currentPeriod' => 0, 'previousPeriod' => 0);
    private $overallRatingsAvg = array('currentPeriod' => 0, 'previousPeriod' => 0);
    private $ratingsByStarsChartData = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
    private $ratingsByStarsChartConfig = array(
        'width' => '100',
        'height' => '350px',
        'paddingTop' => 20,
        'paddingBottom' => 20,
        'borderHeight' => 1,
        'lineColor' => '#F59450',
        'nameColor' => '#815C87',
        'valueColor' => '#78287D',
        'lineHeight' => '',
        'lineHeightStyle' => '',
        'maxEvalValue' => 0,
    );
    
    public function indexAction($alphanumericValue)
    {
        $identifier = $this->getDoctrine()->getRepository('AcmeRatingBundle:Identifier')->findOneByAlphanumericValue($alphanumericValue);

        $collection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->findOneByIdentifier($identifier);
        if ( empty($collection) ) {
            throw $this->createNotFoundException('The rateable collection does not exists.');
        }
        
        $rateables = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->findBy(array(
            'collection' => $collection,
            'isActive' => true,
        ));

        $content = $this->renderView('AcmeRatingBundle:RateableCollection:index.html.twig', array(
            'collection' => $collection,
            'rateables' => $rateables,
            'rateableImageURLs' => $this->getImageURLsForRateablesInCollection($collection),
        ));

        return new Response($content);
    }

    public function publicProfileAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        $rateables = $this->getRateablesWithAverageAndCount($rateableCollection->getRateables());
        $ratings = $this->getRatingsForRateables($rateableCollection->getRateables());
        $collectionImageURL = $this->getImageURLForCollection($rateableCollection);
        
        return $this->render('AcmeRatingBundle:RateableCollection:publicProfile.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'rateablesData' => $this->getRateablesWithAverageAndCount($rateableCollection->getRateables()),
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverageWithTwoDecimals($ratings),
            'collectionImageURL' => $collectionImageURL,
        ));
    }

    private function getRateablesWithAverageAndCount($rateables)
    {
        $rateablesById = array();
       
        foreach($rateables AS $rateable) {
            $rateablesById[$rateable->getId()] = array();
            $rateablesById[$rateable->getId()]['rateable'] = $rateable;
            $rateablesById[$rateable->getId()]['ratingsCount'] = count($rateable->getRatings());
            $rateablesById[$rateable->getId()]['ratingsAverage'] = $this->getRatingsAverageWithTwoDecimals($rateable->getRatings());
        }
        
        return $rateablesById;
    }

    public function profileAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        $ratings = $this->getRatingsForRateables($rateableCollection->getRateables());
        $collectionImageURL = $this->getImageURLForCollection($rateableCollection);
        
        return $this->render('AcmeRatingBundle:RateableCollection:profile.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'ratings' => $ratings,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverageWithTwoDecimals($ratings),
            'collectionImageURL' => $collectionImageURL,
        ));
    }

    private function getRatingsForRateables($rateables)
    {
        $ratings = array();
        
        foreach($rateables AS $rateable) {
            foreach($rateable->getRatings() AS $rating) {
                $ratings[$rating->getCreated()->getTimeStamp()] = $rating;
            }
        }
        
        if ( !empty($ratings) ) {
            krsort($ratings, SORT_NUMERIC);
        }
        
        return $ratings;
    }

    private function getRatingsAverageWithTwoDecimals($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();
        
        if ( count($ratings) == 0 )
            return 0.0;

        $average = (float)$ratingSum / (float)count($ratings);
        $average = round($average, 2);
        
        return number_format($average, 2, ',', ' ');
    }
    
    public function editAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        $rateables = $rateableCollection->getRateables();
        $collectionImageURL = $this->getImageURLForCollection($rateableCollection);

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeRatingBundle:RateableCollection:edit.html.twig', array(
            'rateableCollection' => $rateableCollection,
            'rateables' => $rateables,
            'imageUploadForm' => $imageUploadForm->createView(),
            'collectionImageURL' => $collectionImageURL,
        ));
    }

    public function archiveAction(Request $request, Rateable $rateable) {
        $isActive = $request->get('isActive');
        $rateable->setIsActive($isActive);
        $rateable->getRateableUser()->setIsActive($isActive);
        $this->getDoctrine()->getManager()->flush();

        return new Response('OK');
    }

    private function getImageURLForCollection($rateableCollection)
    {
        $collectionImageURL = null;
        $collectionImage = $rateableCollection->getImage();
        if ( !empty($collectionImage) ) {
            $collectionImageURL = $collectionImage->getWebPath();
        }
        
        return $collectionImageURL;
    }

    private function getImageURLsForRateablesInCollection($rateableCollection)
    {
        $imageURLs = array();

        foreach($rateableCollection->getRateables() AS $rateable)
            $imageURLs[$rateable->getId()] = $this->getImageURL($rateable);
        
        return $imageURLs;
    }

    private function getImageURL($rateable)
    {
        $imageURL = null;
        $image = $rateable->getImage();
        if ( !empty($image) ) {
            $imageURL = $image->getWebPath();
        }
        
        return $imageURL;
    }

    public function uploadImageAction($id)
    {
        $rateableCollection = $this->getRateableCollectionById($id);
        
        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $imageUploadForm->bind($this->getRequest());

            if ( $imageUploadForm->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $rateableCollection->setImage($image);
                $rateableCollection->logUpdated();
                $entityManager->persist($image);
                $entityManager->persist($rateableCollection);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $id)));
            }
        }
    }

    private function getRateableCollectionById($id)
    {
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($id);
        if ( empty($rateableCollection) ) {
            throw $this->createNotFoundException('RateableCollection could not be found.');
        }
        
        return $rateableCollection;
    }

    public function updateAction()
    {
        $collection = $this->getRateableCollectionFromRequest();

        $collection->setName($this->getRequest()->request->get('collectionName'));
        $collection->setForeignURL($this->getRequest()->request->get('collectionForeignURL'));
        $collection->logUpdated();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($collection);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $collection->getId())));
    }

    public function newRateableForCollectionAction()
    {
        $collection = $this->getRateableCollectionFromRequest();
        $rateableName = $this->getRequest()->request->get('rateableName');
        $rateableTypeName = $this->getRequest()->request->get('rateableTypeName');

        if ( empty($rateableName) and empty($rateableTypeName) ) {
            return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $collection->getId())));
        }

        $rateable = new Rateable();
        $rateable->setName($rateableName);
        $rateable->setTypeName($rateableTypeName);
        $rateable->setCollection($collection);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rateable);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $collection->getId())));
    }

    private function getRateableCollectionFromRequest()
    {
        $rateableCollectionId = $this->getRequest()->request->get('rateableCollectionId');
        $rateableCollection = $this->getDoctrine()->getRepository('AcmeRatingBundle:RateableCollection')->find($rateableCollectionId);
        if ( empty($rateableCollection) ) {
            throw $this->createNotFoundException('The rateable collection does not exists.');
        }

        return $rateableCollection;
    }

    public function updateRateableForCollectionAction()
    {
        $rateable = $this->getRateableFromRequest();
        
        $rateable->setName($this->getRequest()->request->get('rateableName'));
        $rateable->setTypeName($this->getRequest()->request->get('rateableTypeName'));
        $rateable->logUpdated();
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rateable);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('rateable_collection_profile_edit_by_id', array('id' => $this->getRequest()->request->get('rateableCollectionId'))));
    }

    private function getRateableFromRequest()
    {
        $rateableId = $this->getRequest()->request->get('rateableId');
        $rateable = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rateable')->find($rateableId);
        if ( empty($rateable) ) {
            throw $this->createNotFoundException('The rateable does not exists.');
        }

        return $rateable;
    }

    public function reportSelectorAction() {
        $ownedCollections = $this->getUserFromContext()->getOwnedCollections();
        if ( empty($ownedCollections) ) {
            return new Response('<html><body>Nincs hozzáférése egyetlen üzlet riportjának megtekintéséhez sem.</body></html>');
        }
        
        return $this->render('AcmeRatingBundle:RateableCollection:reportSelector.html.twig', array(
            'ownedCollections' => $ownedCollections,
            'defaultStartDateString' => date("Y-m-d", strtotime("-1 months")),
            'defaultEndDateString' => date("Y-m-d"),
        ));
    }
    
    private function getUserFromContext()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) ) {
            throw $this->createNotFoundException('Current user could not be found.');
        }

        return $user;
    }

    public function reportAction()
    {
        if ( !$this->getRequest()->isMethod('POST') ) {
            return;
        }

        $this->reportCollection = $this->getRateableCollectionById($this->getRequest()->request->get('rateableCollectionId'));

        if ( empty($this->reportCollection) ) {
            throw $this->createNotFoundException('Rateable collection not found!');
        }
        else if ( !$this->getUserFromContext()->getOwnedCollections()->contains($this->reportCollection) ) {
            throw $this->createNotFoundException('Current user has no right no access to this rateableCollection.');
        }
        
        $this->reportCurrentPeriod = array(
            'startDate' => \DateTime::createFromFormat("Y-m-d H:i:s", "{$this->getRequest()->request->get('startDateString')} 00:00:00"),
            'endDate' => \DateTime::createFromFormat("Y-m-d H:i:s", "{$this->getRequest()->request->get('endDateString')} 23:59:59"),
        );

        if ( !Validator::isEndDateLaterThanStartDateByAlmostOneDay($this->reportCurrentPeriod['startDate'], $this->reportCurrentPeriod['endDate']) ) {
            return new Response('<html><body>Hibás kezdő és vég dátumok!</body></html>');
        }
        
        $this->calcPreviousPeriodWithSameLength();
        $this->loadReportDataForPeriod();

        $this->rateableReportsDataSortedByRatingAverage = $this->rateableReportsData;
        uasort($this->rateableReportsDataSortedByRatingAverage, array($this, 'rateableReportsDataCompareByRatingAverage'));

        $this->rateableReportsDataSortedByRatingCount = $this->rateableReportsData;
        uasort($this->rateableReportsDataSortedByRatingCount, array($this, 'rateableReportsDataCompareByRatingCount'));
        
        return $this->render('AcmeRatingBundle:RateableCollection:report.html.twig', array(
            'title' => $this->reportCurrentPeriod['startDate']->format("Y.m.d.")." – ".$this->reportCurrentPeriod['endDate']->format("Y.m.d."),
            'rateableReportsDataSortedByRatingAverage' => $this->rateableReportsDataSortedByRatingAverage,
            'rateableReportsDataSortedByRatingCount' => $this->rateableReportsDataSortedByRatingCount,
            'rateableAveragesChartData' => $this->rateableAveragesChartData,
            'rateableRatingCountsChartData' => $this->rateableRatingCountsChartData,
            'overallRatingAverageByMonthChartData' => $this->overallRatingAverageByMonthChartData,
            'overallContactsCount' => $this->overallContactsCount,
            'overallRatingsCount' => $this->overallRatingsCount,
            'overallRatingsAvg' => $this->overallRatingsAvg,
            'ratingsByStarsChartConfig' => $this->ratingsByStarsChartConfig,
            'ratingsByStarsChartData' => $this->ratingsByStarsChartData,
            'contactCountByDayChartData' => $this->contactCountByDayChartData,
            'ratingCountByDayChartData' => $this->ratingCountByDayChartData,
            'ratingAvgByDayChartData' => $this->ratingAvgByDayChartData,
        ));
    }

    private function calcPreviousPeriodWithSameLength() {
        $diffTimestamp = $this->reportCurrentPeriod['endDate']->getTimestamp() - $this->reportCurrentPeriod['startDate']->getTimestamp();
        $this->reportPreviousPeriod['startDate'] = new \DateTime();
        $this->reportPreviousPeriod['startDate']->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp() - $diffTimestamp);
        $this->reportPreviousPeriod['endDate'] = $this->reportCurrentPeriod['startDate'];
    }

    private function loadReportDataForPeriod() {
        $this->rateableReportsData = array();
        
        $this->initOverallRatingAverageByDayChartData();
        $this->initContactCountByDayChartData();
        $this->initRatingCountByDayChartData();
        $this->initRatingAvgByDayChartData();

        $this->loadGetRateablesForCollectionStatement();
        $this->processGetRateablesForCollectionStatement();

        $this->loadGetContactsForCollectionStatement();
        $this->processGetContactsForCollectionStatement();
        $this->postProcessGetContactsForCollectionStatement();

        $this->loadGetRatingsAverageForLastTwelveMonthsStatement();
        $this->processGetRatingsAverageForLastTwelveMonthsStatement();

        $this->loadGetRatingsForCollectionStatement();
        $this->processGetRatingsForCollectionStatement();
        $this->postProcessGetRatingsForCollectionStatement();
        
        $this->calcRatingsByStarsChartConfig();
    }

    private function initOverallRatingAverageByDayChartData() {
        $firstDayOfMonth = new \DateTime();
        $firstDayOfMonth->setTimestamp($this->reportCurrentPeriod['endDate']->getTimestamp());
        $firstDayOfMonth->modify('first day of this month');
        $firstDayOfMonth->modify('-12 month');
        $this->overallRatingAverageByMonthChartData = array(
            'globalAverage' => 0,
            'dataByMonths' => array(),
        );
   
        for($monthIndex=0; $monthIndex<12; ++$monthIndex) {
            $this->overallRatingAverageByMonthChartData['dataByMonths']["{$firstDayOfMonth->format('Y-m')}"] = array(
                'sum' => 0,
                'count' => 0,
                'avg' => 0,
            );

            $firstDayOfMonth->modify('+1 month');
        } 
    }

    private function initContactCountByDayChartData() {
        $currentDay = new \DateTime();
        $currentDay->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp());
        $this->contactCountByDayChartData = array();
        
        while( $currentDay->getTimestamp() <= $this->reportCurrentPeriod['endDate']->getTimestamp() ) {
            $this->contactCountByDayChartData['day'][$currentDay->format('Y-m-d')] = 0;
            $currentDay->modify('+1 day');
        }
    }

    private function initRatingCountByDayChartData() {
        $currentDay = new \DateTime();
        $currentDay->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp());
        $this->ratingCountByDayChartData = array();
        
        while( $currentDay->getTimestamp() <= $this->reportCurrentPeriod['endDate']->getTimestamp() ) {
            $this->ratingCountByDayChartData['day'][$currentDay->format('Y-m-d')] = 0;
            $currentDay->modify('+1 day');
        }
    }

    private function initRatingAvgByDayChartData() {
        $currentDay = new \DateTime();
        $currentDay->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp());
        $this->ratingAvgByDayChartData = array();
        
        while( $currentDay->getTimestamp() <= $this->reportCurrentPeriod['endDate']->getTimestamp() ) {
            $this->ratingAvgByDayChartData['day'][$currentDay->format('Y-m-d')]['sum'] = 0;
            $this->ratingAvgByDayChartData['day'][$currentDay->format('Y-m-d')]['count'] = 0;
            $this->ratingAvgByDayChartData['day'][$currentDay->format('Y-m-d')]['avg'] = 0;
            $currentDay->modify('+1 day');
        }
    }
    
    private function loadGetRateablesForCollectionStatement() {
        $connection = $this->get('database_connection');
        $queryText = sprintf($this->getRateablesForCollectionQueryText, $this->reportCollection->getId());
        $this->getRateablesForCollectionStatement = $connection->executeQuery($queryText);
        $this->getRateablesForCollectionStatement->execute();
    }
    
    private function processGetRateablesForCollectionStatement() {
        foreach($this->getRateablesForCollectionStatement->fetchAll() AS $record) {
            $id = $record['rateableId'];
            $name = $record['rateableName'];
            
            $this->rateableReportsData[$id]['name'] = $name;
            $this->rateableReportsData[$id]['profilePictureURL'] = '';
            
            $this->rateableReportsData[$id]['previousPeriod']['contactCount'] = 0;
            $this->rateableReportsData[$id]['previousPeriod']['ratingCount'] = 0;
            $this->rateableReportsData[$id]['previousPeriod']['ratingsSum'] = 0;
            $this->rateableReportsData[$id]['previousPeriod']['ratingsAvg'] = 0;

            $this->rateableReportsData[$id]['currentPeriod']['contactCount'] = 0;
            $this->rateableReportsData[$id]['currentPeriod']['ratingCount'] = 0;
            $this->rateableReportsData[$id]['currentPeriod']['ratingsSum'] = 0;
            $this->rateableReportsData[$id]['currentPeriod']['ratingsAvg'] = 0;
            
            if ( !empty($record['imageFileName']) ) {
                $this->rateableReportsData[$id]['profilePictureURL'] = "/uploads/images/{$record['imageFileName']}.{$record['imageFileExtension']}";
            }

            $this->rateableRatingCountsChartData[$name] = 0;
        }
    }
    
    private function loadGetContactsForCollectionStatement() {
        $connection = $this->get('database_connection');
        $queryText = sprintf($this->getContactsForCollectionQueryText, 
            $this->reportCollection->getId(), 
            $this->reportPreviousPeriod['startDate']->format("Y-m-d H:i:s"),
            $this->reportCurrentPeriod['endDate']->format("Y-m-d H:i:s")
        );
        $this->getContactsForCollectionStatement = $connection->executeQuery($queryText);
        $this->getContactsForCollectionStatement->execute();
    }

    private function processGetContactsForCollectionStatement() {
        foreach($this->getContactsForCollectionStatement->fetchAll() AS $record) {
            $id = $record['rateableId'];
            $contactTimestamp = strtotime($record['contactHappenedAt']);
            $contactDateTime = new \DateTime();
            $contactDateTime->setTimestamp($contactTimestamp);

            if ( $this->isTimestampInPreviousPeriod($contactTimestamp) ) {
                ++$this->rateableReportsData[$id]['previousPeriod']['contactCount'];
                ++$this->overallContactsCount['previousPeriod'];
            }
            elseif ( $this->isTimestampInCurrentPeriod($contactTimestamp) ) {
                ++$this->rateableReportsData[$id]['currentPeriod']['contactCount'];
                ++$this->overallContactsCount['currentPeriod'];
                ++$this->contactCountByDayChartData['day'][$contactDateTime->format('Y-m-d')];
            }
        }
    }

    private function postProcessGetContactsForCollectionStatement() {
        $maxContactCount = 0;
        foreach($this->contactCountByDayChartData['day'] AS $contactCount) {
            if ( $maxContactCount < $contactCount ) {
                $maxContactCount = $contactCount;
            }
        }

        $highestValueInChart = 1.1 * $maxContactCount;
        
        $unitTime = ( $this->reportCurrentPeriod['endDate']->getTimestamp() - $this->reportCurrentPeriod['startDate']->getTimestamp() ) / 100;
        for($point=0; $point<100; ++$point) {
            $currentDateTime = new \DateTime();
            $currentDateTime->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp() + ($point * $unitTime));

            $previousDay = new \DateTime();
            $previousDay->setTimestamp($currentDateTime->getTimestamp());
            $previousDay->setTime(0, 0, 0);

            $nextDay = new \DateTime();
            $nextDay->setTimestamp($currentDateTime->getTimestamp());
            $nextDay->setTime(0, 0, 0);
            $nextDay->modify('+1 day');

            $x = $currentDateTime->getTimestamp();
            $x1 = $previousDay->getTimestamp();
            $x2 = $nextDay->getTimestamp();
            $y1 = $this->contactCountByDayChartData['day'][$previousDay->format('Y-m-d')];
            $y2 = $this->contactCountByDayChartData['day'][$nextDay->format('Y-m-d')];

            if ( empty($highestValueInChart) ) {
                $this->contactCountByDayChartData['values'][$point] = 0;
            }
            else {
                $this->contactCountByDayChartData['values'][$point] = ( $y1+($x-$x1)*($y2-$y1)/($x2-$x1) ) / $highestValueInChart;
            }
        }
    }

    private function loadGetRatingsAverageForLastTwelveMonthsStatement() {
        $startDate = new \DateTime();
        $startDate->setTimestamp($this->reportCurrentPeriod['endDate']->getTimestamp());
        $startDate->modify('first day of this month');
        $startDate->modify('-12 month');

        $connection = $this->get('database_connection');
        $queryText = sprintf($this->getRatingsAverageForLastTwelveMonthsQueryText,
            $startDate->format("Y-m-d H:i:s"),
            $this->reportCurrentPeriod['endDate']->format("Y-m-d H:i:s")
        );
        $this->getRatingsAverageForLastTwelveMonthsStatement = $connection->executeQuery($queryText);
        $this->getRatingsAverageForLastTwelveMonthsStatement->execute();
    }

    private function processGetRatingsAverageForLastTwelveMonthsStatement() {
        foreach($this->getRatingsAverageForLastTwelveMonthsStatement->fetchAll() AS $record) {
            $this->overallRatingAverageByMonthChartData['globalAverage'] = floatval($record['rating_average']);
            break;
        }
    }

    private function loadGetRatingsForCollectionStatement() {
        $startDate = new \DateTime();
        $startDate->setTimestamp($this->reportCurrentPeriod['endDate']->getTimestamp());
        $startDate->modify('first day of this month');
        $startDate->modify('-12 month');

        $connection = $this->get('database_connection');
        $queryText = sprintf($this->getRatingsForCollectionQueryText, 
            $this->reportCollection->getId(),
            $startDate->format("Y-m-d H:i:s"),
            $this->reportCurrentPeriod['endDate']->format("Y-m-d H:i:s")
        );
        $this->getRatingsForCollectionStatement = $connection->executeQuery($queryText);
        $this->getRatingsForCollectionStatement->execute();
    }

    private function processGetRatingsForCollectionStatement() {
        foreach($this->getRatingsForCollectionStatement->fetchAll() AS $record) {
            $id = $record['rateableId'];
            $ratingTimestamp = strtotime($record['ratingReceivedAt']);
            $ratingDateTime = new \DateTime();
            $ratingDateTime->setTimestamp($ratingTimestamp);
            
            if ( array_key_exists($ratingDateTime->format('Y-m'), $this->overallRatingAverageByMonthChartData['dataByMonths']) ) {
                $this->overallRatingAverageByMonthChartData['dataByMonths']["{$ratingDateTime->format('Y-m')}"]['sum'] += $record['stars'];
                ++$this->overallRatingAverageByMonthChartData['dataByMonths']["{$ratingDateTime->format('Y-m')}"]['count'];
            }
            
            if ( $this->isTimestampInPreviousPeriod($ratingTimestamp) ) {
                $this->rateableReportsData[$id]['previousPeriod']['ratingsSum'] += $record['stars'];
                ++$this->rateableReportsData[$id]['previousPeriod']['ratingCount'];

                $this->overallRatingsSum['previousPeriod'] += $record['stars'];
                ++$this->overallRatingsCount['previousPeriod'];
            }
            elseif ( $this->isTimestampInCurrentPeriod($ratingTimestamp) ) {
                $this->rateableReportsData[$id]['currentPeriod']['ratingsSum'] += $record['stars'];
                ++$this->rateableReportsData[$id]['currentPeriod']['ratingCount'];
                
                $this->overallRatingsSum['currentPeriod'] += $record['stars'];
                ++$this->overallRatingsCount['currentPeriod'];

                ++$this->ratingsByStarsChartData[$record['stars']];

                ++$this->ratingCountByDayChartData['day'][$ratingDateTime->format('Y-m-d')];

                $this->ratingAvgByDayChartData['day'][$ratingDateTime->format('Y-m-d')]['sum'] += $record['stars'];
                ++$this->ratingAvgByDayChartData['day'][$ratingDateTime->format('Y-m-d')]['count'];

                $rateableName = $this->rateableReportsData[$id]['name'];
                ++$this->rateableRatingCountsChartData[$rateableName];
            }
        }
    }

    private function postProcessGetRatingsForCollectionStatement() {
        foreach($this->rateableReportsData AS $id => $rateableData) {
            foreach(array('currentPeriod', 'previousPeriod') AS $periodName) {
                $avg = 0.0;
                if ( !empty($this->rateableReportsData[$id][$periodName]['ratingCount']) ) {
                    $avg = $this->rateableReportsData[$id][$periodName]['ratingsSum'] / $this->rateableReportsData[$id][$periodName]['ratingCount'];
                }

                $this->rateableReportsData[$id][$periodName]['ratingsAvg'] = $avg;
                
                if ( $periodName === 'currentPeriod' ) {
                    $rateableName = $this->rateableReportsData[$id]['name'];
                    $this->rateableAveragesChartData[$rateableName] = $avg;
                }
            }
        }

        foreach($this->overallRatingAverageByMonthChartData['dataByMonths'] AS $date => $statistics) {
            if ( empty($statistics['count']) ) {
                $this->overallRatingAverageByMonthChartData['dataByMonths'][$date]['avg'] = 0;
            }
            else {
                $this->overallRatingAverageByMonthChartData['dataByMonths'][$date]['avg'] = $statistics['sum'] / $statistics['count'];
            }
        }

        foreach(array('currentPeriod', 'previousPeriod') AS $periodName) {
            if ( empty($this->overallRatingsCount[$periodName]) ) {
                $this->overallRatingsAvg[$periodName] = 0;
            }
            else {
                $this->overallRatingsAvg[$periodName] = $this->overallRatingsSum[$periodName] / $this->overallRatingsCount[$periodName];
            }
        }
        
        $maxRatingCount = 0;
        foreach($this->ratingCountByDayChartData['day'] AS $ratingCount) {
            if ( $maxRatingCount < $ratingCount ) {
                $maxRatingCount = $ratingCount;
            }
        }
        
        $highestValueInChart = 1.1 * $maxRatingCount;
        
        $unitTime = ( $this->reportCurrentPeriod['endDate']->getTimestamp() - $this->reportCurrentPeriod['startDate']->getTimestamp() ) / 100;
        for($point=0; $point<100; ++$point) {
            $currentDateTime = new \DateTime();
            $currentDateTime->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp() + ($point * $unitTime));

            $previousDay = new \DateTime();
            $previousDay->setTimestamp($currentDateTime->getTimestamp());
            $previousDay->setTime(0, 0, 0);

            $nextDay = new \DateTime();
            $nextDay->setTimestamp($currentDateTime->getTimestamp());
            $nextDay->setTime(0, 0, 0);
            $nextDay->modify('+1 day');

            $x = $currentDateTime->getTimestamp();
            $x1 = $previousDay->getTimestamp();
            $x2 = $nextDay->getTimestamp();
            $y1 = $this->ratingCountByDayChartData['day'][$previousDay->format('Y-m-d')];
            $y2 = $this->ratingCountByDayChartData['day'][$nextDay->format('Y-m-d')];

            if ( empty($highestValueInChart) ) {
                $this->ratingCountByDayChartData['values'][$point] = 0;
            }
            else {
                $this->ratingCountByDayChartData['values'][$point] = ( $y1+($x-$x1)*($y2-$y1)/($x2-$x1) ) / $highestValueInChart;
            }
        }
        
        $maxRatingAvg = 0;
        foreach($this->ratingAvgByDayChartData['day'] AS $dateString => $stats) {
            if ( empty($this->ratingAvgByDayChartData['day'][$dateString]['count']) ) {
                $this->ratingAvgByDayChartData['day'][$dateString]['avg'] = 0;
            }
            else {
                $this->ratingAvgByDayChartData['day'][$dateString]['avg'] = 
                    $this->ratingAvgByDayChartData['day'][$dateString]['sum'] / 
                    $this->ratingAvgByDayChartData['day'][$dateString]['count'];
            }
            
            if ( $maxRatingAvg < $this->ratingAvgByDayChartData['day'][$dateString]['avg'] ) {
                $maxRatingAvg = $this->ratingAvgByDayChartData['day'][$dateString]['avg'];
            }
        }

        $highestValueInChart = 1.1 * $maxRatingAvg;

        $unitTime = ( $this->reportCurrentPeriod['endDate']->getTimestamp() - $this->reportCurrentPeriod['startDate']->getTimestamp() ) / 100;
        for($point=0; $point<100; ++$point) {
            $currentDateTime = new \DateTime();
            $currentDateTime->setTimestamp($this->reportCurrentPeriod['startDate']->getTimestamp() + ($point * $unitTime));

            $previousDay = new \DateTime();
            $previousDay->setTimestamp($currentDateTime->getTimestamp());
            $previousDay->setTime(0, 0, 0);

            $nextDay = new \DateTime();
            $nextDay->setTimestamp($currentDateTime->getTimestamp());
            $nextDay->setTime(0, 0, 0);
            $nextDay->modify('+1 day');

            $x = $currentDateTime->getTimestamp();
            $x1 = $previousDay->getTimestamp();
            $x2 = $nextDay->getTimestamp();
            $y1 = $this->ratingAvgByDayChartData['day'][$previousDay->format('Y-m-d')]['avg'];
            $y2 = $this->ratingAvgByDayChartData['day'][$nextDay->format('Y-m-d')]['avg'];

            if ( empty($highestValueInChart) ) {
                $this->ratingAvgByDayChartData['values'][$point] = 0;
            }
            else {
                $this->ratingAvgByDayChartData['values'][$point] = ( $y1+($x-$x1)*($y2-$y1)/($x2-$x1) ) / $highestValueInChart;
            }
        }
    }

    private function isTimestampInPreviousPeriod($timestamp) {
        if ( ( $this->reportPreviousPeriod['startDate']->getTimestamp() <= $timestamp ) AND ( $timestamp < $this->reportPreviousPeriod['endDate']->getTimestamp() ) ) {
            return true;
        }

        return false;
    }

    private function isTimestampInCurrentPeriod($timestamp) {
        if ( ( $this->reportCurrentPeriod['startDate']->getTimestamp() <= $timestamp ) AND ( $timestamp < $this->reportCurrentPeriod['endDate']->getTimestamp() ) ) {
            return true;
        }

        return false;
    }

    private function calcRatingsByStarsChartConfig() {
        $this->ratingsByStarsChartConfig['style'] = sprintf('background-color: #ffffff;%1$s%2$s',
            empty($this->ratingsByStarsChartConfig['height']) ? '' : "height: {$this->ratingsByStarsChartConfig['height']};",
            empty($this->ratingsByStarsChartConfig['width']) ? '' : "width: {$this->ratingsByStarsChartConfig['width']};"
        );

        $this->ratingsByStarsChartConfig['height'] = (int) filter_var($this->ratingsByStarsChartConfig['height'], FILTER_SANITIZE_NUMBER_INT);
        
        $this->ratingsByStarsChartConfig['lineHeight'] = floor(
            (
                $this->ratingsByStarsChartConfig['height'] 
                - $this->ratingsByStarsChartConfig['paddingTop'] 
                - $this->ratingsByStarsChartConfig['paddingBottom'] 
                - (4*$this->ratingsByStarsChartConfig['borderHeight'])
            ) 
            / count($this->ratingsByStarsChartData)
        );

        $this->ratingsByStarsChartConfig['lineHeightStyle'] = 'height: '.$this->ratingsByStarsChartConfig['lineHeight'].'px; line-height: '.$this->ratingsByStarsChartConfig['lineHeight'].'px';

        $this->ratingsByStarsChartConfig['maxEvalValue'] = 0;
        foreach ($this->ratingsByStarsChartData AS $star => $count) {
            if ($count > $this->ratingsByStarsChartConfig['maxEvalValue']) {
                $this->ratingsByStarsChartConfig['maxEvalValue'] = $count;
            }
        }
    }

    private function rateableReportsDataCompareByRatingAverage($a, $b) {
        if ( $a['currentPeriod']['ratingsAvg'] == $b['currentPeriod']['ratingsAvg'] ) {
            return 0;
        }

        return ( $a['currentPeriod']['ratingsAvg'] < $b['currentPeriod']['ratingsAvg'] ) ? 1 : -1;
    }

    private function rateableReportsDataCompareByRatingCount($a, $b) {
        if ( $a['currentPeriod']['ratingCount'] == $b['currentPeriod']['ratingCount'] ) {
            return 0;
        }

        return ( $a['currentPeriod']['ratingCount'] < $b['currentPeriod']['ratingCount'] ) ? 1 : -1;
    }
}

