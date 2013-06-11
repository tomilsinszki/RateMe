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
}

?>

