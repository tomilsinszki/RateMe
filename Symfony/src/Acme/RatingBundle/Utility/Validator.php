<?php

namespace Acme\RatingBundle\Utility;

class Validator
{
    public static function isEmailAddressValid($emailAddress)
    {
        if ( empty($emailAddress) ) {
            return false;
        }

        if ( filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false ) {
            return false;
        }

        return true;
    }

    public static function isEndDateLaterThanStartDateByAlmostOneDay($startDateTime, $endDateTime)
    {
        if ( Validator::isDateTimeObject($startDateTime) === false ) {
            return false;
        }
        
        if ( Validator::isDateTimeObject($endDateTime) === false ) {
            return false;
        }

        $diffSeconds = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();
        if ( $diffSeconds >= ( 60 * 60 * 24 - 1 ) ) {
            return true;
        }
        
        return false;
    }

    public static function isDateTimeObject($dateTimeObject)
    {
        if ( is_object($dateTimeObject) ) {
            if ( get_class($dateTimeObject) === 'DateTime' ) {
                return true;
            }
        }
        
        return false;
    }
}

?>

