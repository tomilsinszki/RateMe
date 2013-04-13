$(document).ready(function(){
    var emailPrefixLastLoaded = '';
    var autocompleteForEmail = new Array();
    var autocompleteDataByEmail = null;
    var autocompleteList = $("#email_input_autocomplete_list");

    function setUpSelectEmailEvent() {
        $(".emailInputAutocompleteListItem").click(function() {
            var selectedEmail = $(this).text();
            
            $("#form_email").val(selectedEmail);
            $("#email_input_content").html('<span class="written">'+selectedEmail+'</span>');
            autocompleteList.html('');
            
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

    function updateEmailAutocompleteList(content) {
        var content = $("#form_email").val();

        if ( content.length < 3 ) {
            autocompleteList.html('');
            return;
        }
        
        if ( autocompleteForEmail.length == 0 ) {
            autocompleteList.html('');
            return;
        }

        var autocompleteListInnerHTML = '';
        var autocompleteListCount = 0;
        for (var i=0; i<autocompleteForEmail.length; ++i) {
            var email = autocompleteForEmail[i];
            var prefixLength = content.length;

            if ( 5 <= autocompleteListCount ) {
                break;
            }

            if ( content.toLowerCase() == email.substring(0, prefixLength).toLowerCase() ) {
                ++autocompleteListCount;
                autocompleteListInnerHTML += '<li class="emailInputAutocompleteListItem">'+autocompleteForEmail[i]+'</li>';
            }
        }
        
        autocompleteList.html('<ul>'+autocompleteListInnerHTML+'</ul>');

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

    $("#form_clientId").keyup(function() {
        var content = $("#form_clientId").val();
        $("#client_id_input_content").html('<span class="written">'+content+'</span>');
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
