var setUpInputLabel = function(elementId, label) {
    $('#'+elementId).focus(function() {
        if ( $('#'+elementId).attr('value') == label ) {
            $('#'+elementId).attr('value', '');
            $('#'+elementId).css('color', '#000000');
        }
    });

    $('#'+elementId).blur(function() {
        if ( $('#'+elementId).attr('value') == '' ) {
            $('#'+elementId).attr('value', label);
            $('#'+elementId).css('color', '#666666');
        }
    });

    if ( $('#'+elementId).val() == '' ) {
        $('#'+elementId).attr('value', label);
        $('#'+elementId).css('color', '#666666');
    }
}

