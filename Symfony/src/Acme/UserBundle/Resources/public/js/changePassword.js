$(document).ready(function(){

    function isOldPasswordValid(password) {
        var isOldPasswordValid = false;

        $.ajax({
            url: '/felhasznalo/jelszo/ervenyese',
            type: 'POST',
            data: {password: password},
            async: false
        }).done(
            function(isPasswordValid) {
                isOldPasswordValid = isPasswordValid;
            }
        );

        return isOldPasswordValid;
    }

    function isFormValid() {
        var oldPassword = $('#form_oldPassword').val();
        var newPassword1 = $('#form_newPassword1').val();
        var newPassword2 = $('#form_newPassword2').val();

        if ( newPassword1 != newPassword2 ) {
            return false;
        }

        if ( newPassword1 == '' ) {
            return false;
        }

        if ( newPassword1.lenght <= 4 ) {
            return false;
        }

        if ( isOldPasswordValid(oldPassword) == false ) {
            return false
        }

        return true;
    }

    function enableSubmitButtonIfFormValid() {
        if ( isFormValid() == true ) {
            $('#form_submit').attr('disabled', false);
        }
        else {
            $('#form_submit').attr('disabled', true);
        }
    }

    $('#form_oldPassword').blur(function() {
        var password = $('#form_oldPassword').val();

        if ( isOldPasswordValid(password) ) {
            $('#oldPassword_ajax_error').empty();
        }
        else {
            $('#oldPassword_ajax_error').html('Hibás jelszó!');
        }
    });

    $('#form_newPassword1').blur(function() {
        var password = $('#form_newPassword1').val();

        if ( password.length < 4 ) {
            $('#newPassword1_ajax_error').html('Ez a jelszó túl rövid!');
        }
        else {
            $('#newPassword1_ajax_error').empty();
        }
    });

    $('#form_newPassword1').keyup(function() {
        enableSubmitButtonIfFormValid();
    });

    $('#form_newPassword2').keyup(function() {
        enableSubmitButtonIfFormValid();
    });

});

