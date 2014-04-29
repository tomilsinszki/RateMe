$(document).ready(function(){
    var rateableCollectionNameLabel = 'Hely neve';
    var rateableCollectionForeignURLLabel = 'Hely honlap címe';
    var newRateableNameLabel = 'Név';
    var newRateableTypeNameLabel = 'Beosztás';

    $('.rateableIsArchive').on('click', function () {
        var $this = $(this),
            $profileLink = $this.parent().parent().find('.profile-link');

        $this.attr('disabled', 'disabled');
        $.ajax({
            url: $this.attr('data-url'),
            type: 'POST',
            data: 'isActive=' + ($this.is(':checked') ? 0 : 1),
            success: function (response) {
                if (response === 'OK') {
                    $this.removeAttr('disabled');

                    $profileLink.toggleClass('gray');
                    if ($profileLink.attr('href') === '#') {
                        $profileLink.attr('href', $profileLink.data('href'));
                    } else {
                        $profileLink.attr('href', '#');
                    }
                } else {
                    alert(response);
                }
            }
        });
    });
    
    $('#rateableCollectionEditForm').submit(function() {
        if ( $('#rateableCollectionName').attr('value') == rateableCollectionNameLabel )
            $('#rateableCollectionName').attr('value', '');

        if ( $('#rateableCollectionForeignURL').attr('value') == rateableCollectionForeignURLLabel )
            $('#rateableCollectionForeignURL').attr('value', '');
    });

    $('#newRateableForm').submit(function() {
        if ( $('#newRateableName').attr('value') == newRateableNameLabel )
            $('#newRateableName').attr('value', '');

        if ( $('#newRateableTypeName').attr('value') == newRateableTypeNameLabel )
            $('#newRateableTypeName').attr('value', '');
    });

    (function () {
        var $inactiveContainer = $('#inactive-rateables');

        $('#active-rateables').children().each(function () {
            if ($(this).find('.profile-link').hasClass('gray')) {
                $inactiveContainer.append($(this).detach());
            }
        });
    }());
});
