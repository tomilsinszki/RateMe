$(document).ready(function(){
    var usernameLabel = 'Felhasználónév';
    var emailAddressLabel = 'Email cím';
    var passwordLabel = 'Jelszó';
    var passwordAgainLabel = 'Jelszó újra';

    setUpInputLabel('form_username', usernameLabel);
    setUpInputLabel('form_email', emailAddressLabel);
    setUpInputLabel('form_password', passwordLabel);
    setUpInputLabel('form_password_again', passwordAgainLabel);

    $('#form_registration').submit(function() {
        if ( $('#form_username').attr('value') == usernameLabel )
            $('#form_username').attr('value', '');
        
        if ( $('#form_email').attr('value') == emailAddressLabel )
            $('#form_email').attr('value', '');
        
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
