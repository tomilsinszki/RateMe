<?php

namespace Acme\UserBundle\Utility;

class CurrentUser
{
    public static function getCollectionIfOwner($securityContext)
    {
        $user = $securityContext->getToken()->getUser();

        if ( empty($user) === TRUE ) {
            return null;
        }

        if ( CurrentUser::isOfRole($securityContext, 'ROLE_MANAGER') === FALSE ) {
            return null;
        }

        $ownedCollections = $user->getOwnedCollections()->toArray();
        if ( count($ownedCollections) <= 0 ) {
            return null;
        }

        return array_pop($ownedCollections);
    }

    public static function isOfRole($securityContext, $roleName)
    {
        if ( $securityContext->isGranted($roleName) === TRUE ) {
            return TRUE;
        }

        return FALSE;
    }
}
