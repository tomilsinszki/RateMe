$(document).ready(function(){
    var rateableCollectionNameLabel = 'Hely neve';
    var rateableCollectionForeignURLLabel = 'Hely honlap címe';
    var newRateableNameLabel = 'Név';
    var newRateableTypeNameLabel = 'Beosztás';

    $('.rateableIsArchive').on('click', function () {
        var $this = $(this);

        $this.attr('disabled', 'disabled');
        $.ajax({
            url: $this.attr('data-url'),
            data: 'isActive=' + ($this.is(':checked') ? 0 : 1),
            success: function (response) {
                if (response === 'OK') {
                    $this.removeAttr('disabled');
                } else {
                    alert(response);
                }
            }
        });
    });

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
