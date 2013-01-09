function isEmailValid(email) {
    var emailRegExp = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
    return ( email.search(emailRegExp) != -1 );
}

function generateErrorMessageIfUsernameAlreadyExists(username) {
    $.ajax({
        url: "/felhasznalo/letezike",
        type: "post",
        dataType: "json",
        data: "username="+username
    }).done(function(doesUserExist) {
        if ( doesUserExist == true )
            $('#username_ajax_error').html('Az e-mail cím már foglalt.');
        else
            $('#username_ajax_error').empty();
    });
}

$(document).ready(function(){
    var usernameLabel = 'Email cím';
    var passwordLabel = 'Jelszó';
    var passwordAgainLabel = 'Jelszó újra';

    setUpInputLabel('form_username', usernameLabel);
    setUpInputLabel('form_password', passwordLabel);
    setUpInputLabel('form_password_again', passwordAgainLabel);

    $('#form_password').focus(function() {
        var username = $('#form_username').val();

        if ( isEmailValid(username) == false ) {
            $('#username_ajax_error').html('Az e-mail cím nem tűnik érvényesnek.');
        }
        else {
            $('#username_ajax_error').empty();
            generateErrorMessageIfUsernameAlreadyExists(username);
        }
    });

    $('#form_password_again').focus(function() {
        var password = $('#form_password').val();

        if ( ( password.length < 4 ) || ( password == passwordLabel ) )
            $('#password_ajax_error').html('Ez a jelszó túl rövid.');
        else
            $('#password_ajax_error').empty();
    });

    $('#form_registration').submit(function() {
        if ( $('#form_username').attr('value') == usernameLabel )
            $('#form_username').attr('value', '');
        
        if ( $('#form_password').attr('value') == passwordLabel )
            $('#form_password').attr('value', '');
        
        if ( $('#form_password_again').attr('value') == passwordAgainLabel )
            $('#form_password_again').attr('value', '');
    });

    $('#form_password').keyup(function() {
        if ( $('#form_password').attr('value') == passwordLabel )
            return false;

        if ( $('#form_password_again').attr('value') == passwordAgainLabel )
            return false;

        if ( $('#form_password').attr('value') == $('#form_password_again').attr('value') )
            $('#form_submit').attr('disabled', false);
    });

    $('#form_password_again').keyup(function() {
        if ( $('#form_password').attr('value') == passwordLabel )
            return false;

        if ( $('#form_password_again').attr('value') == passwordAgainLabel )
            return false;

        if ( $('#form_password').attr('value') == $('#form_password_again').attr('value') )
            $('#form_submit').attr('disabled', false);
    });
});
