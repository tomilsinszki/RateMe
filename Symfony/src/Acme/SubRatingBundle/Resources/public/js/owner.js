$('document').ready(function() {
    var ajaxBaseUrl = $('.questionnaireForm').attr('data-ajax-route');
    var isQuestionBeingEdited = false;
    
    $('#sortable').sortable({
        items: 'li:not(.ui-state-disabled)',      
        update: function(event, ui) {
            sequenceChange();
        }
    });
    
    function sequenceChange() {
        var questionIdsInNewSequence = new Array();

        $.each($('#sortable li.questionRow'), function(key, questionContainer) {
            if ( key == 'length' ) {
                return;
            }
            
            $(questionContainer).find('div.questionSequence').text(key+1);
            questionIdsInNewSequence.push($(questionContainer).attr('data-id'));
        });

        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/sorrend/valtozas",
            type: 'POST',
            dataType: "json",
            data: {
                rateableCollectionId: $('#rateableCollectionSelect').val(),
                questionIdsInNewSequence: questionIdsInNewSequence
            },
            async: false
        });
    }
    
    $('#rateableCollectionSelect').change(function() {
        var rateableCollectionId = $(this).val();
        window.location = ajaxBaseUrl + "melysegi/kerdoiv/"+rateableCollectionId;
    });
    
    $('input[name=listType]').change(function() {
        var that = $(this);
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/sorrend/tipus/valtozas",
            type: 'POST',
            data: {
                questionOrderId: $(that).attr('data-id'),
                rateableCollectionId: $('#rateableCollectionSelect').val(),
            },
            async: true
        });
    });
    
    $("#createNewQuestionButton").on('click', function() {
        if ( isQuestionBeingEdited ) {
            return;
        }
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/uj/kerdes/urlap",
            type: 'GET',
            dataType: 'html',
            async: false
        }).done(function(innerHTML) {
            unbindAllEvents();
            var newQuestionSequence = getLargestQuestionSequence()+1;
            $('#sortable').append(innerHTML);
            $('#createQuestionContainer div.questionSequence').text(newQuestionSequence);
            startEditingQuestion();
            $("#createQuestionContainer .selectInput").chosen({disable_search_threshold: 10});
            bindAllEvents();
        });
    });

    function getLargestQuestionSequence() {
        var largestQuestionSequence = 0;

        $.each($('div.questionTableRow div.col-1'), function(key, sequenceContainer) {
            if ( key == 'length' ) {
                return;
            }

            var currentQuestionSequence = parseInt($(sequenceContainer).text());

            if ( largestQuestionSequence < currentQuestionSequence ) {
                largestQuestionSequence = currentQuestionSequence;
            }
        });

        return largestQuestionSequence;
    }

    function bindQuestionTypeSelectChangeEvent() {
        $('select.questionTypeSelect').on('change', function(event) {
            event.stopPropagation();
            var innerHTML = '';

            if ( 1 == $(this).val() ) {
                $.ajax({
                    url: ajaxBaseUrl + "melysegi/kerdoiv/uj/kerdes/alurlap/igennem",
                    type: 'GET',
                    dataType: 'html',
                    async: false
                }).done(function(html) {
                    innerHTML = html;
                });
            }
            else if ( 2 == $(this).val() ) {
                $.ajax({
                    url: ajaxBaseUrl + "melysegi/kerdoiv/uj/kerdes/alurlap/skala",
                    type: 'GET',
                    dataType: 'html',
                    async: false
                }).done(function(html) {
                    innerHTML = html;
                });
            }
            
            unbindAllEvents();
            $(this).closest('li.questionRow').find('div.questionAnswerRow').html(innerHTML);
            bindAllEvents();
        });
    }

    function unbindQuestionTypeSelectChangeEvent() {
        $('select.questionTypeSelect').off('change');
    }

    function bindSaveButtonClickEvent() {
        $(".saveQuestionButton").on('click', function(event) {
            event.stopPropagation();
            var questionContainer = $(this).closest('li');
            var questionId = $(questionContainer).attr('data-id');

            if ( questionId ) {
                if ( 1 == $(questionContainer).find('select.questionTypeSelect').val() ) {
                    modifyYesNoTypeOfQuestion(questionContainer);
                }
                else if ( 2 == $(questionContainer).find('select.questionTypeSelect').val() ) {
                    modifyScaleTypeOfQuestion(questionContainer);
                }
            }
            else {
                if ( 1 == $(questionContainer).find('select.questionTypeSelect').val() ) {
                    createYesNoTypeOfQuestion(questionContainer);
                }
                else if ( 2 == $(questionContainer).find('select.questionTypeSelect').val() ) {
                    createScaleTypeOfQuestion(questionContainer);
                }
            }
        });
    }

    function modifyYesNoTypeOfQuestion(questionContainer) {
        var parameters = getParametersForYesNoQuestion(questionContainer);
        
        if ( areParametersValid(parameters) == false ) {
            return;
        }
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/modositas",
            type: 'POST',
            data: parameters,
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            location.reload();
        });
    }

    function modifyScaleTypeOfQuestion(questionContainer) {
        var parameters = getParametersForScaleQuestion(questionContainer);
        
        if ( areParametersValid(parameters) == false ) {
            return;
        }
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/modositas",
            type: 'POST',
            data: parameters,
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            location.reload();
        });
    }

    function createYesNoTypeOfQuestion(questionContainer) {
        var parameters = getParametersForYesNoQuestion(questionContainer);
        
        if ( areParametersValid(parameters) == false ) {
            return;
        }
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/letrehozas",
            type: 'POST',
            data: parameters,
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            location.reload();
        });
    }

    function createScaleTypeOfQuestion(questionContainer) {
        var parameters = getParametersForScaleQuestion(questionContainer);
        
        if ( areParametersValid(parameters) == false ) {
            return;
        }
        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/letrehozas",
            type: 'POST',
            data: parameters,
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            location.reload();
        });
    }

    function getParametersForYesNoQuestion(questionContainer) {
        return {
            questionId: parseInt($(questionContainer).attr('data-id')),
            rateableCollectionId: parseInt($('#rateableCollectionSelect').val()),
            title: $(questionContainer).find('input.questionTitleInput').val(),
            text: $(questionContainer).find('input.questionTextInput').val(),
            typeId: parseInt($(questionContainer).find('select.questionTypeSelect').val()),
            target: parseInt($(questionContainer).find('select.targetSelect').val()),
            answerYesText: $(questionContainer).find('input.inputAnswer_yes').val(),
            answerNoText: $(questionContainer).find('input.inputAnswer_no').val(),
            answerNaText: $(questionContainer).find('input.inputAnswer_na').val(),
            isAnswerNaEnabled: $(questionContainer).find('input.naAvailableCheckbox').is(':checked')
        };
    }

    function getParametersForScaleQuestion(questionContainer) {
        return {
            questionId: parseInt($(questionContainer).attr('data-id')),
            rateableCollectionId: parseInt($('#rateableCollectionSelect').val()),
            title: $(questionContainer).find('input.questionTitleInput').val(),
            text: $(questionContainer).find('input.questionTextInput').val(),
            typeId: parseInt($(questionContainer).find('select.questionTypeSelect').val()),
            target: parseInt($(questionContainer).find('select.targetSelect').val()),
            answerOneText: $(questionContainer).find('input.inputAnswer_1').val(),
            answerTwoText: $(questionContainer).find('input.inputAnswer_2').val(),
            answerThreeText: $(questionContainer).find('input.inputAnswer_3').val(),
            answerFourText: $(questionContainer).find('input.inputAnswer_4').val(),
            answerFiveText: $(questionContainer).find('input.inputAnswer_5').val(),
            answerNaText: $(questionContainer).find('input.inputAnswer_na').val(),
            isAnswerNaEnabled: $(questionContainer).find('input.naAvailableCheckbox').is(':checked')
        };
    }

    function areParametersValid(parameters) {
        var areParametersValid = true;
        
        $.each(parameters, function(key, parameter) {
            if ('length' == key) {
                return;
            }
            
            if ('answerNaText' == key) {
                return;
            }
            
            if ('isAnswerNaEnabled' == key) {
                return;
            }
            
            if ( 'number' == typeof(parameter) ) {
                areParametersValid = ( parameter <= 0 ) ? false : areParametersValid;
                return;
            }
            else if ( 'string' == typeof(parameter) ) {
                areParametersValid = ( parameter == '' ) ? false : areParametersValid;
                return;
            }
        });
        
        if ( parameters.isAnswerNaEnabled ) {
            areParametersValid = ( parameters.answerNaText == '' ) ? false : areParametersValid;
        }
        
        return areParametersValid;
    }

    function unbindSaveButtonClickEvent() {
        $(".saveQuestionButton").off('click');
    }

    function bindIsNaAnswerEnabledCheckboxChangeEvent() {
        $("input.naAvailableCheckbox").on('change', function (event) {
            event.stopPropagation();

            var naInput = $(this).parent().parent().find("input.inputAnswer_na");
            
            if ( $(this).is(':checked') ) {
                $(naInput).removeAttr('disabled');
                $(naInput).removeClass('naInputDisabled');
            }
            else {
                $(naInput).attr('disabled','disabled');
                $(naInput).addClass('naInputDisabled');
            }
        });
    }

    function unbindIsNaAnswerEnabledCheckboxChangeEvent() {
        $("input.naAvailableCheckbox").off('change');
    }

    function bindDeleteButtonClickEvent() {
        $('div.deleteButton').on('click', function(event) {
            event.stopPropagation();
            var questionId = $(this).closest('li').attr('data-id');
            var that = $(this);
            
            if ( questionId ) {
                $("#dialog-delete-confirm").dialog({
                    resizable: false,
                    modal: true,
                    buttons: {
                        "Biztos": function() {
                            $(this).dialog("close");
                            $.ajax({
                                url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/torles",
                                type: 'POST',
                                data: {
                                    questionId: $(that).closest('li').attr('data-id')
                                },
                                async: false
                            }).done(function(data) {                              
                                stopEditingQuestion();
                                $(that).closest('li').remove();                      
                                sequenceChange();
                            });                  
                        },
                        "Mégsem": function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
            else {
                $('#createQuestionContainer').remove();
                stopEditingQuestion();
            }
        });
    }
    bindDeleteButtonClickEvent();

    function unbindDeleteButtonClickEvent() {
        $('div.deleteButton').off('click');
    }
    
    function bindCancelButtonClickEvent() {
        $("div.cancelQuestionButton").on('click', function(event) {
            event.stopPropagation();
            
            var questionContainer = $(this).closest('li');

            if( $(questionContainer).attr('data-id') ) {
                unbindAllEvents();

                $("span.questionSpan").each(function() {
                    previousValue = $(this).attr('data-prev');
                    $(this).parent().find("input.questionInput").val(previousValue);
                    $(this).text(previousValue);
                });

                $("span.questionSelectSpan").each(function() {
                    previousValue = $(this).attr('data-prev-text');
                    var selectInput = $(this).parent().find(".selectInput");
                    var selectInputOption = $(this).parent().find(".selectInput option").filter(function() {
                        return $(this).text() == previousValue
                    });

                    $(selectInputOption).prop('selected', true);
                    $(selectInput).trigger('liszt:updated');
                    
                    $(this).text(previousValue);
                });

                $("input.questionInput").hide();
                $(".selectInput").hide();
                $('.chzn-container').hide();
                $("span.questionSpan").show();
                $("span.questionSelectSpan").show();
                $(questionContainer).find("div.questionAnswerRow").html("");
                bindAllEvents();
            }

            $('#createQuestionContainer').remove();
            stopEditingQuestion();
        });
    }

    function unbindCancelButtonClickEvent() {
        $("div.cancelQuestionButton").off('click');
    }

    function bindQuestionRowClickEvent() {
        $("li.questionRow div.questionTableRow").on('click', function(event) {
            event.stopPropagation();

            if ( isQuestionBeingEdited ) {
                return;
            }
            
            var questionContainer = $(this).closest("li.questionRow");
            var questionId = $(questionContainer).attr('data-id');
            
            if ( questionId ) {
                $.ajax({
                    url: ajaxBaseUrl + "melysegi/kerdoiv/kerdes/valaszok",
                    type: 'POST',
                    data: {
                        questionId: questionId,
                    },
                    async: false
                }).done(function(innerHTML) {
                    unbindAllEvents();
                    startEditingQuestion();
                    $(questionContainer).find("div.questionAnswerRow").html(innerHTML);
                    setDashAsTextIfAnswerNAEmpty();
                    bindAllEvents();
                });
            }
            
        });
    }
    bindQuestionRowClickEvent();

    function unbindQuestionRowClickEvent() {
        $("li.questionRow div.questionTableRow").off('click');
    }

    function bindQuestionSpanClickEvent() {
        $("li.questionRow span.questionSpan").on("click", function(event) {
            hideSpanAndShowInput($(this));
        });
    }

    function unbindQuestionSpanClickEvent() {
        $("li.questionRow span.questionSpan").off("click");
    }

    function bindQuestionSelectSpanClickEvent() {
        $("li.questionRow .questionSelectSpan").on("click", function(event) {
            hideSpanAndShowInput($(this));
        });
    }

    function unbindQuestionSelectSpanClickEvent() {
        $("li.questionRow .questionSelectSpan").off("click");
    }

    function hideSpanAndShowInput(spanElement) {
        var questionContainer = $(spanElement).closest("li.questionRow");
        var questionId = $(questionContainer).attr('data-id');
        
        if ( !questionId ) {
            return;
        }
        
        if ( $(questionContainer).find("div.questionAnswerRow").html() == '' ) {
            return;
        }

        $("span.questionSpan").each(function() {
            var value = $(this).parent().find("input.questionInput").val();
            $(this).text(value);
        });

        setDashAsTextIfAnswerNAEmpty();

        $("span.questionSelectSpan").each(function() {
            var value = $(this).parent().find(".selectInput option:selected").text();
            $(this).text(value);
        });
        
        $("input.questionInput").hide();
        $(".selectInput").hide();
        $('.chzn-container').hide();
        $("span.questionSpan").show();
        $("span.questionSelectSpan").show();

        $(spanElement).hide();
        $(spanElement).parent().find(".selectInput").chosen({disable_search_threshold: 10});
        $(spanElement).parent().find(".chzn-container").show();
        $(spanElement).parent().find("input.questionInput").show();
        $(spanElement).parent().find("input.questionInput").focus();
    }

    function setDashAsTextIfAnswerNAEmpty() {
        $("span.spanAnswer_na").each(function() {
            var value = $(this).parent().find("input.questionInput").val();

            if ( value == "" ) {
                $(this).text("–");
            }
        });
    }

    function bindAllEvents() {
        bindQuestionTypeSelectChangeEvent();
        bindCancelButtonClickEvent();
        bindSaveButtonClickEvent();
        bindIsNaAnswerEnabledCheckboxChangeEvent();
        bindDeleteButtonClickEvent();
        bindQuestionRowClickEvent();
        bindQuestionSpanClickEvent();
        bindQuestionSelectSpanClickEvent();
    }

    function unbindAllEvents() {
        unbindQuestionTypeSelectChangeEvent();
        unbindCancelButtonClickEvent();
        unbindSaveButtonClickEvent();
        unbindIsNaAnswerEnabledCheckboxChangeEvent();
        unbindDeleteButtonClickEvent();
        unbindQuestionRowClickEvent();
        unbindQuestionSpanClickEvent();
        unbindQuestionSelectSpanClickEvent();
    }
    
    function startEditingQuestion() {
        $('#sortable').sortable('disable');
        isQuestionBeingEdited = true;
    }
    
    function stopEditingQuestion() {
        $('#createQuestionContainer').removeClass('ui-state-disabled');
        $('#sortable').sortable('enable');
        isQuestionBeingEdited = false;
    }
});

