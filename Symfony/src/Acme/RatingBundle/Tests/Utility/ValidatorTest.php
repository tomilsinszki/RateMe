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
}
