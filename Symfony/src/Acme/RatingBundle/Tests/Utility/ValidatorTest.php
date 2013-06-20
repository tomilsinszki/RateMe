<?php

namespace Acme\RatingBundle\Tests\Utility;

use Acme\RatingBundle\Utility\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsEmailAddressInvalid()
    {
        $invalidEmailAddresses = array(
            '',
            ' ',
            '_',
            'istvan.kokenm',
            'lukacsnetunde',
            'neme*',
            '65suzy@gmail. com',
            'felföldirozi@freemail.hu',
            'okoraab.@kabelnbet.hu',
            'rences@kabelnet.',
            'toth.sandor.nsz.@gmail.com',
            'eszter.almássy@gmail.com',
            'vinkovics.zsuzsa',
            'e _balazs@enternet.hu',
            'lakibalazs.jako@gmail. com',
            'fodorsvkabelnet.hu',
            'boros.@kabelnet.hu',
            'csoszogaborvfreemail.hu',
            'álom@freemail.hu',
        );

        foreach($invalidEmailAddresses AS $email) {
            $this->assertFalse(Validator::isEmailAddressValid($email));
        }
    }

    public function testIsEmailAddressValid()
    {
        $validEmailAddresses = array(
            'sumegiattila@gmail.com',
            'm.dani@t-online.hu',
            'ketrin65@citromail.hu',
            'emilia.0511@gmail.com',
            'mesterpont1@gmail.com',
            'hajnalka.karikas@gmail.com',
            'albiadmin@kabelnet.hu',
            'batizerzsike@gmail.com',
            'ilsinszkitamas@gmail.com',
        );

        foreach($validEmailAddresses AS $email) {
            $this->assertTrue(Validator::isEmailAddressValid($email));
        }
    }

    public function testIsDateTimeObjectInvalid() {
        $invalidDateTimeObjects = array(
            '',
            'apple',
            12,
            '#',
            null,
        );

        foreach($invalidDateTimeObjects AS $dateTimeObject) {
            $this->assertFalse(Validator::isDateTimeObject($dateTimeObject));
        }
    }

    public function testIsDateTimeObjectValid() {
        $validDateTimeObjects = array(
            \DateTime::createFromFormat("Y-m-d H:i:s", "2013-01-01 00:00:00"),
            \DateTime::createFromFormat("Y-m-d H:i:s", "2013-12-01 00:00:00"),
        );

        foreach($validDateTimeObjects AS $dateTimeObject) {
            $this->assertTrue(Validator::isDateTimeObject($dateTimeObject));
        }
    }

    public function testIfEndDateLaterThanStartDateByAtLeastOneDay() {
        $datePairs = array(
            array('start' => '2013-01-01 00:00:00', 'end' => '2013-01-02 00:00:00'),
            array('start' => '2013-01-01 00:00:00', 'end' => '2013-01-03 00:00:00'),
            array('start' => '2013-01-01 00:00:00', 'end' => '2013-02-01 00:00:00'),
            array('start' => '2013-01-01 00:00:00', 'end' => '2013-02-03 00:00:00'),
        );

        foreach($datePairs AS $datePair) {
            $this->assertTrue(Validator::isEndDateLaterThanStartDateByAtLeastOneDay(
                \DateTime::createFromFormat("Y-m-d H:i:s", $datePair['start']),
                \DateTime::createFromFormat("Y-m-d H:i:s", $datePair['end'])
            ));
        }
    }

    public function testIfEndDateNotLaterThanStartDateByAtLeastOneDay() {
        $datePairs = array(
            array('start' => '2013-01-15 00:00:00', 'end' => '2013-01-15 00:00:00'),
            array('start' => '2013-01-15 00:00:00', 'end' => '2013-01-15 12:00:00'),
            array('start' => '2013-01-15 00:00:00', 'end' => '2013-01-15 23:59:59'),
            array('start' => '2013-01-15 00:00:00', 'end' => '2013-01-14 23:59:59'),
            array('start' => '2013-01-15 00:00:00', 'end' => ''),
            array('start' => '', 'end' => '2013-01-14 23:59:59'),
            array('start' => '', 'end' => ''),
        );

        foreach($datePairs AS $datePair) {
            $this->assertFalse(Validator::isEndDateLaterThanStartDateByAtLeastOneDay(
                \DateTime::createFromFormat("Y-m-d H:i:s", $datePair['start']),
                \DateTime::createFromFormat("Y-m-d H:i:s", $datePair['end'])
            ));
        }
    }
}

?>

