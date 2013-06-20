<?php

namespace Acme\RatingBundle\Utility;

class Validator
{
    public static function isEmailAddressValid($emailAddress)
    {
        if ( empty($emailAddress) === TRUE ) {
            return FALSE;
        }

        if ( filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === FALSE ) {
            return FALSE;
        }

        return TRUE;
    }

    public static function isEndDateLaterThanStartDateByAtLeastOneDay($startDateTime, $endDateTime)
    {
        if ( Validator::isDateTimeObject($startDateTime) === FALSE ) {
            return FALSE;
        }
        
        if ( Validator::isDateTimeObject($endDateTime) === FALSE ) {
            return FALSE;
        }

        $diffSeconds = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();
        $diffDays = $diffSeconds / ( 60 * 60 * 24 );

        if ( $diffDays >= 1.0 ) {
            return TRUE;
        }

        return FALSE;
    }

    public static function isDateTimeObject($dateTimeObject)
    {
        if ( is_object($dateTimeObject) === TRUE ) {
            if ( get_class($dateTimeObject) === 'DateTime' ) {
                return TRUE;
            }
        }
        
        return FALSE;
    }
}

?>

