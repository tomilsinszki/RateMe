<?php

namespace Acme\RatingBundle\Event;

class ModifyRateableCollectionIdentifierEvent extends ModifyIdentifierEvent {

    public function getQrCodeString() {
        return 'http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2F{{id}}&size=250x250';
    }

} 