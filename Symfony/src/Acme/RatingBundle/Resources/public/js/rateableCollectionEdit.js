$(document).ready(function(){
    var rateableCollectionNameLabel = 'Hely neve';
    var rateableCollectionForeignURLLabel = 'Hely honlap címe';
    var newRateableNameLabel = 'Név';
    var newRateableTypeNameLabel = 'Beosztás';

    setUpInputLabel('rateableCollectionName', rateableCollectionNameLabel);
    setUpInputLabel('rateableCollectionForeignURL', rateableCollectionForeignURLLabel);
    setUpInputLabel('newRateableName', newRateableNameLabel);
    setUpInputLabel('newRateableTypeName', newRateableTypeNameLabel);

    $('#rateableCollectionEditForm').submit(function() {
        if ( $('#rateableCollectionName').attr('value') == rateableCollectionNameLabel )
            $('#rateableCollectionName').attr('value', '');

        if ( $('#rateableCollectionForeignURL').attr('value') == rateableCollectionForeignURLLabel )
            $('#rateableCollectionForeignURL').attr('value', '');
    });

    $('#newRateableForm').submit(function(event) {
        if ( $('#newRateableName').attr('value') == newRateableNameLabel )
            $('#newRateableName').attr('value', '');

        if ( $('#newRateableTypeName').attr('value') == newRateableTypeNameLabel )
            $('#newRateableTypeName').attr('value', '');
    });
});
