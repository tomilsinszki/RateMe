$(document).ready(function(){
    var emailPrefixLastLoaded = '';
    var autocompleteForEmail = new Array();
    var autocompleteDataByEmail = null;
    var autocompleteListForEmail = $("#email_input_autocomplete_list");

    var autocompleteForClientId = new Array();
    var autocompleteDataByClientId = null;
    var autocompleteListForClientId = $("#client_id_input_autocomplete_list");

    function setUpSelectEmailEvent() {
        $(".emailInputAutocompleteListItem").click(function() {
            var selectedEmail = $(this).text();
            
            $("#form_email").val(selectedEmail);
            $("#email_input_content").html('<span class="written">'+selectedEmail+'</span>');
            autocompleteListForEmail.html('');
            
            autocompleteData = autocompleteDataByEmail[selectedEmail];
            
            var clientId = autocompleteData.clientId;
            if ( clientId != null ) {
                $("#form_clientId").val(clientId);
                $("#client_id_input_content").html('<span class="written">'+clientId+'</span>');
            }

            var lastName = autocompleteData.lastName;
            if ( lastName != null ) {
                $("#form_lastName").val(lastName);
                $("#last_name_input_content").html('<span class="written">'+lastName+'</span>');
            }

            var firstName = autocompleteData.firstName;
            if ( firstName != null ) {
                $("#form_firstName").val(firstName);
                $("#first_name_input_content").html('<span class="written">'+firstName+'</span>');
            }
        });
    }

    function updateEmailAutocompleteList() {
        var content = $("#form_email").val();

        if ( content.length < 3 ) {
            autocompleteListForEmail.html('');
            return;
        }
        
        if ( autocompleteForEmail.length == 0 ) {
            autocompleteListForEmail.html('');
            return;
        }

        var autocompleteListForEmailInnerHTML = '';
        var autocompleteListCount = 0;
        for (var i=0; i<autocompleteForEmail.length; ++i) {
            var email = autocompleteForEmail[i];
            var prefixLength = content.length;

            if ( 5 <= autocompleteListCount ) {
                break;
            }

            if ( content.toLowerCase() == email.substring(0, prefixLength).toLowerCase() ) {
                ++autocompleteListCount;
                autocompleteListForEmailInnerHTML += '<li class="emailInputAutocompleteListItem">'+autocompleteForEmail[i]+'</li>';
            }
        }
        
        autocompleteListForEmail.html('<ul>'+autocompleteListForEmailInnerHTML+'</ul>');

        setUpSelectEmailEvent();
    }

    $("#form_email").keyup(function() {
        var content = $("#form_email").val();
        $("#email_input_content").html('<span class="written">'+content+'</span>');

        var emailPrefix = content.substring(0, 3);
        
        updateEmailAutocompleteList();
        
        var emailListEmpty = ( (3<content.length) && (autocompleteForEmail.length==0) );
        var emailDataEmpty = ( (3<content.length) && (autocompleteDataByEmail==null) );
        var randomReload = ( (3<content.length) && (Math.random()<0.2) );
        var hasEmailPrefixChanged = ( (3<=content.length) && (emailPrefix!=emailPrefixLastLoaded) );

        var shouldAutocompleteReload = ( emailListEmpty || emailDataEmpty || randomReload || hasEmailPrefixChanged );
        if ( shouldAutocompleteReload ) {
            emailPrefixLastLoaded = emailPrefix;

            $.ajax({
                url: "/ugyfel/kontakt/email/autocomplete",
                type: 'POST',
                dataType: "json",
                data: {emailPrefix: content.substring(0, 3)},
                async: true
            }).done(function(returnData) {
                autocompleteForEmail = returnData.emails;
                autocompleteDataByEmail = returnData.dataByEmail;
                updateEmailAutocompleteList();
            });
        }
    });

    function setUpSelectClientIdEvent() {
        $(".clientIdInputAutocompleteListItem").click(function() {
            var selectedClientId = $(this).text();
            
            $("#form_clientId").val(selectedClientId);
            $("#client_id_input_content").html('<span class="written">'+selectedClientId+'</span>');
            autocompleteListForClientId.html('');
            
            autocompleteData = autocompleteDataByClientId[selectedClientId];

            var emailAddress = autocompleteData.emailAddress;
            if ( emailAddress != null ) {
                $("#form_email").val(emailAddress);
                $("#email_input_content").html('<span class="written">'+emailAddress+'</span>');
            }

            var lastName = autocompleteData.lastName;
            if ( lastName != null ) {
                $("#form_lastName").val(lastName);
                $("#last_name_input_content").html('<span class="written">'+lastName+'</span>');
            }

            var firstName = autocompleteData.firstName;
            if ( firstName != null ) {
                $("#form_firstName").val(firstName);
                $("#first_name_input_content").html('<span class="written">'+firstName+'</span>');
            }
        });
    }

    function updateClientIdAutocompleteList() {
        var content = $("#form_clientId").val();
        
        if ( autocompleteListForClientId.length == 0 ) {
            autocompleteListForClientId.html('');
            return;
        }

        var autocompleteListForClientIdInnerHTML = '';
        var autocompleteListCount = 0;
        for (var i=0; i<autocompleteForClientId.length; ++i) {
            var clientId = autocompleteForClientId[i];

            if ( 5 <= autocompleteListCount ) {
                break;
            }

            if ( content == clientId ) {
                ++autocompleteListCount;
                autocompleteListForClientIdInnerHTML += '<li class="clientIdInputAutocompleteListItem">'+autocompleteForClientId[i]+'</li>';
            }
        }
        
        autocompleteListForClientId.html('<ul>'+autocompleteListForClientIdInnerHTML+'</ul>');

        setUpSelectClientIdEvent();
    }

    $("#form_clientId").keyup(function() {
        var content = $("#form_clientId").val();
        $("#client_id_input_content").html('<span class="written">'+content+'</span>');
        
        $.ajax({
            url: "/ugyfel/kontakt/azonosito/autocomplete",
            type: 'POST',
            dataType: "json",
            data: {clientId: content},
            async: true
        }).done(function(returnData) {
            autocompleteForClientId = returnData.clientIds;
            autocompleteDataByClientId = returnData.dataByClientId;
            updateClientIdAutocompleteList();
        });
    });

    $("#form_lastName").keyup(function() {
        var content = $("#form_lastName").val();
        $("#last_name_input_content").html('<span class="written">'+content+'</span>');
    });

    $("#form_firstName").keyup(function() {
        var content = $("#form_firstName").val();
        $("#first_name_input_content").html('<span class="written">'+content+'</span>');
    });

});
