$(document).ready(function(){

    function isEmailValid(email) {
        var emailRegExp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        return ( email.search(emailRegExp) != -1 );
    }

    function doesUsernameExist(username) {
        var doesUsernameExist = false;

        $.ajax({
            url: "/felhasznalo/letezike",
            type: 'POST',
            dataType: "json",
            data: {username: username},
            async: false
        }).done(function(doesUserExist) {
            doesUsernameExist = doesUserExist;
        });

        return doesUsernameExist;
    }

    function isFormValid() {
        var username = $('#form_username').val();
        var password1 = $('#form_password').val();
        var password2 = $('#form_password_again').val();

        if ( password1 == '' ) {
            return false;
        }

        if ( password2 == '' ) {
            return false;
        }

        if ( password1 != password2 ) {
            return false;
        }

        if ( password1.length < 4 ) {
            return false;
        }

        if ( doesUsernameExist(username) == true ) {
            return false;
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

    $('#form_username').blur(function() {
        var username = $('#form_username').val();

        if ( isEmailValid(username) == false ) {
            $('#username_ajax_error').html('Az e-mail cím nem tűnik érvényesnek.');
        }
        else if( doesUsernameExist(username) == true ) {
            $('#username_ajax_error').html('Az e-mail cím már foglalt.');
        }
        else {
            $('#username_ajax_error').empty();
        }
    });

    $('#form_password').blur(function() {
        var password = $('#form_password').val();

        if ( password.length < 4 ) {
            $('#password_ajax_error').html('Ez a jelszó túl rövid.');
        }
        else {
            $('#password_ajax_error').empty();
        }
    });

    $('#form_password').keyup(function() {
        enableSubmitButtonIfFormValid();
    });

    $('#form_password_again').keyup(function() {
        enableSubmitButtonIfFormValid();
    });

});

