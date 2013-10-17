$('document').ready(function() {
    var editing = false;     
    var lastClickedRowLi = null;
    var listTypeClicked = false;
    var ajaxBaseUrl = $('.questionnaireForm').attr('data-ajax-route');
    $( '.selectInput' ).chosen({disable_search_threshold: 10});
    $('.chzn-container').hide();
    $( '#sortable' ).sortable({
      items: 'li:not(.ui-state-disabled)',      
      update: function( event, ui ) {
          sequenceChange();
      }
    });

    $('#rateableCollectionSelector').bind('change', function () {
        var rateableCollectionId = $(this).val();
        window.location = ajaxBaseUrl + "melysegi/kerdoiv/"+rateableCollectionId;
    });
    
    function sequenceChange() {
        var Questions = new Array();
        $('#sortable').find('li').
            filter(function(){
              return $(this).hasClass('questionRowLi');
            }).
            each(function(index){
              $(this).find('#sequence').text(index+1);
              Questions.push([$(this).attr('data-id'),$(this).find('#sequence').text()]);
            });
        $.ajax({
            url: ajaxBaseUrl + "melysegi/sorrend/valtozas",
            type: 'POST',
            dataType: "json",
            data: {
                questions: Questions
            },
            async: false
        }).done(function(data) {              
        }); 
    }
    
    $(".addNewQuestion").click(function() {
        if(editing) {
            alert('Új kérdés létrehozása előtt fejezd be az előző szerkesztést!');
            return false;
        }
        var nextSequence = $( '#sortable li' ).filter(function(){return $(this).hasClass('questionRowLi');}).size()+1;
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdestipusok",
            type: 'POST',
            dataType: "json",            
            async: false
        }).done(function(data) {            
            var selectAnswerType = '';
            $.each(data,function() {
                selectAnswerType += '<option value="' + this.id + '">' + this.name + '</option>';                
            });          
            
            $('#sortable').append('\
                <li class="ui-state-default ui-state-disabled questionRowLi">\n\
                    <div class="border"></div>\n\
                    <div class="questionTableRow">\n\
                        <div class="col col-1" id="sequence">' + nextSequence + '</div>\n\
                        <div class="col col-2">\n\
                            <div class="input-wrapper">\n\
                                <input id="title" class="titleInput" type="text" />\n\
                            </div>\n\
                        </div>\n\
                        <div class="col col-3">\n\
                            <div class="input-wrapper">\n\
                                <input id="text" class="textInput" type="text" />\n\
                            </div>\n\
                        </div>\n\
                        <div class="col col-4">\n\
                            <select id="questionType" class="selectInput">\n\
                                ' + selectAnswerType +'\n\
                            </select>\n\
                        </div>\n\
                        <div class="col col-5">\n\
                            <select id="targetType" class="selectInput">\n\
                                <option value="1">egyén</option>\n\
                                <option value="2">üzlet</option>\n\
                            </select>\n\
                        </div>\n\
                        <div class="col col-6"><div class="deleteBtn"></div></div>\n\
                    </div>\n\
                    <div id="QuestionAnswerRow"></div>\n\
                </li>');            
            $('#sortable li').last().find('input[id=title]').focus();
            $('#sortable li').last().find( '#questionType option' ).first().attr('selected','selected');
            $('#sortable li').last().find( '#questionType' ).trigger('change');
            $('#sortable li').last().find( '.selectInput' ).chosen({disable_search_threshold: 10});            
            setEditingTrue();
        });
    });
    
    $(document).on('change','.selectInput[id^="questionType"]',function() {        
        var innerHtml = '';
        if(1 == $(this).val()) {            
            innerHtml = '<div class="answerBox">\n\
                            <div class="answerBoxHeader">\n\
                                <div class="col col-2 noCheckbox">Válasz értéke</div>\n\
                                <div class="col col-3">Válasz szövege</div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">Igen</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="yesInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">Nem</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="noInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2"><input id="naAvailable" class="checkboxInput" type="checkbox"><label class="checkboxInputLabel" for="naAvailable">N/A</label></div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="naInput" disabled="disabled" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4">\n\
                                    <div class="btnCont yellowDeepQuestion cancelButton" id="cancelQuestion">\n\
                                        <div class="btnDeepQuestion">\n\
                                            <h3>\n\
                                                <div>Mégsem</div>\n\
                                            </h3>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div class="btnCont yellowDeepQuestion saveButton" id="saveQuestion">\n\
                                        <div class="btnDeepQuestion">\n\
                                            <h3>\n\
                                                <div>Mentés</div>\n\
                                            </h3>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                         </div>';
            $(this).closest('li').find('#QuestionAnswerRow').html(innerHtml).show();
            
        } else if(2 == $(this).val()) {
            innerHtml = '<div class="answerBox">\n\
                            <div class="answerBoxHeader">\n\
                                <div class="col col-2 noCheckbox">Válasz értéke</div>\n\
                                <div class="col col-3">Válasz szövege</div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">1</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="oneInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">2</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="twoInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">3</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="threeInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">4</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="fourInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2 noCheckbox">5</div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="fiveInput" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4"></div>\n\
                            </div>\n\
                            <div class="answerBoxRow">\n\
                                <div class="col col-2"><input id="naAvailable" class="checkboxInput" type="radio"><label class="checkboxInputLabel" for="naAvailable">N/A</label></div>\n\
                                <div class="col col-3"><div class="input-wrapper"><input id="naInput" disabled="disabled" class="titleInput" type="text" /></div></div>\n\
                                <div class="col col-4">\n\
                                    <div class="btnCont yellowDeepQuestion cancelButton" id="cancelQuestion">\n\
                                        <div class="btnDeepQuestion">\n\
                                            <h3>\n\
                                                <div>Mégsem</div>\n\
                                            </h3>\n\
                                        </div>\n\
                                    </div>\n\
                                    <div class="btnCont yellowDeepQuestion saveButton" id="saveQuestion">\n\
                                        <div class="btnDeepQuestion">\n\
                                            <h3>\n\
                                                <div>Mentés</div>\n\
                                            </h3>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                         </div>';
            $(this).closest('li').find('#QuestionAnswerRow').html(innerHtml).show();
        }
        
    });        
    
    $( ".listTypeRadioLabel" ).hover(function(){
        var listType = $(this).attr('for');
        switch(listType) {
            case 'fix':
                $('.seqOptionHoverWrapper').text('Mindenki ugyanazokat a kérdéseket kapja, az alább megadott sorrendben').show();
            break;
            case 'random':
                $('.seqOptionHoverWrapper').text('Mindenki véletlenszerűen kapja a kérdéseket').show();
            break;
            case 'weight':
                $('.seqOptionHoverWrapper').text('A rendszer véletlenszerűen adja a kérdéseket, de az 1. sorszámút adja a legnagyobb valószínűséggel, a legnagyobb sorszámút a legkisebb valószínűséggel').show();
            break;
            case 'balance':
                $('.seqOptionHoverWrapper').text('Mindenki azt a kérdést kapja először, amire eddig a legkevesebb válasz érkezett').show();
            break;
        }
    },function() {
        if(!listTypeClicked) {
            $('.seqOptionHoverWrapper').hide();
        }
    });
    
    $(document).on('click','.listTypeRadioLabel',function() {  
        listTypeClicked = true;
        var listType = $(this).attr('for');
        switch(listType) {
            case 'fix':
                $('.seqOptionHoverWrapper').text('Mindenki ugyanazokat a kérdéseket kapja, az alább megadott sorrenndben').show();
            break;
            case 'random':
                $('.seqOptionHoverWrapper').text('Mindenki véletlenszerűen kapja a kérdéseket').show();
            break;
            case 'weight':
                $('.seqOptionHoverWrapper').text('A rendszer véletlenszerűen adja a kérdéseket, de az 1. sorszámút adja a legnagyobb valószínűséggel, a legnagyobb sorszámút a legkisebb valószínűséggel').show();
            break;
            case 'balance':
                $('.seqOptionHoverWrapper').text('Mindenki azt a kérdést kapja először, amire eddig a legkevesebb válasz érkezett').show();
            break;
        }
        setTimeout(function(){
            $('.seqOptionHoverWrapper').hide();
            listTypeClicked = false;
        },3000);
    });
    
    $(document).on('change','input[name=listType]',function() {        
        var that = $(this);        
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdes/sorrend/valozas",
            type: 'POST',
            data: {
                questionOrderId: $(that).attr('data-id'),
                rateableCollectionId: $('#rateableCollectionSelector').val(),
            },
            async: true
        }).done(function(data) {                                    
        });
    });
    
    $(document).on('change','#naAvailable',function() {        
        if(0 != $('#naAvailable:checked').length) {
            $(this).closest('.answerBoxRow').find('#naInput').removeAttr('disabled').removeClass('naAvailableDiasabled');            
        } else {
            $(this).closest('.answerBoxRow').find('#naInput').attr('disabled','disabled').addClass('naAvailableDiasabled');
        }
    });
    
    $(document).on('click','.deleteBtn',function() {
        var that = $(this);
        if($(that).closest('li').attr('data-id')) {
            $( "#dialog-delete-confirm" ).dialog({
                resizable: false,
                modal: true,
                buttons: {
                  "Biztos": function() {
                      $( this ).dialog( "close" );
                      $.ajax({
                            url: ajaxBaseUrl + "melysegi/torles",
                            type: 'POST',
                            data: {
                                questionId: $(that).closest('li').attr('data-id')
                            },
                            async: false
                      }).done(function(data) {                              
                            setEditingFalse();
                            $(that).closest('li').remove();                      
                            sequenceChange();
                      });                  
                  },
                  "Mégsem": function() {
                     $( this ).dialog( "close" );
                  }
                }
            });
        } else {
            $(that).closest('li').remove();
            setEditingFalse();
        }
    });
    
    $(document).on('click','.questionTableRow > div[class!="col col-6"]',function() {                
        var that = $(this).closest('.questionTableRow');        
        lastClickedRowLi = $(that).closest('li');
        if(1 != $(that).attr('data-opened') && $(that).closest('li').attr('data-id')) {
            if(editing) {            
                $('.questionTableRow')
                    .filter(function(){
                        return $(this).attr('data-opened') == 1;
                    })
                    .closest('li')
                    .find('#cancelQuestion')
                    .trigger('click');                 
            } else {
                var lastOpenedLi = $('#sortable').find('.questionTableRow').filter(function(){
                    return $(this).attr('data-opened') == 1;
                }).parent();
                hideAllInputShowSpan($(lastOpenedLi));
                $('.questionRowLi').find('#QuestionAnswerRow').html('').hide();
                $('.questionRowLi').find('span[class=preSpan]').removeClass('hoverSpan');            
                $.ajax({
                    url: ajaxBaseUrl + "melysegi/valaszok",
                    type: 'POST',
                    data: {
                        questionId: $(that).closest('li').attr('data-id')
                    },
                    async: false
                }).done(function(data) {  
                    setEditingTrue();
                    $('.questionTableRow').removeAttr('data-opened');
                    $(that).attr('data-opened',1);                
                    $(that).closest('li').find('#QuestionAnswerRow').html(data).show();
                    $(that).closest('li').find('span[class=preSpan]').addClass('hoverSpan');
                    activateEditingInputs($(that).closest('li'));
                });
            }
        }
    });
    
    function activateEditingInputs(liContainer) {
        $(liContainer).find('select').trigger("liszt:updated");
        $(liContainer).find('span').filter(function(){return $(this).hasClass('preSpan');}).click(function() { 
            hideAllSpanWithoutSavePrevValue(liContainer);
            if('questionTypeSpan' == $(this).attr('id') || 'targetTypeSpan' == $(this).attr('id')) {
                $(this).next().trigger('liszt:updated');
                $(this).hide().next().next().show().focus();                
            } else {
                $(this).hide().parent().find('input').show().focus();
            }
        });
        $(liContainer).find('input[type=text]').keypress(function(e){
            if(e.which == 13) {
                hideAllSpanWithoutSavePrevValue(liContainer);
            }
        });
    }
    
    $(document).on('click','#cancelQuestion',function() {                       
        var cancelButton = $(this);        
        var nowOpenedLi = $(cancelButton).closest('li');
        if($(nowOpenedLi).attr('data-id')) {
            if(isQuestionDataEquals(cancelButton)) {
                closeActiveQuestionRow(cancelButton);
                lastClickedRowNotEqualsClickedRow(nowOpenedLi);
            } else {            
                $( "#dialog-cancel-confirm" ).dialog({
                    resizable: false,
                    modal: true,
                    buttons: {
                      "Mentés": function() {
                          $( this ).dialog( "close" );
                          $(cancelButton).parent().find('#saveQuestion').trigger('click');
                          lastClickedRowNotEqualsClickedRow(nowOpenedLi);
                      },
                      "Kilép mentés nélkül": function() {                      
                          $( this ).dialog( "close" );
                          closeActiveQuestionRow(cancelButton);      
                          lastClickedRowNotEqualsClickedRow(nowOpenedLi);
                      }
                    }
                });             
            }
        } else {
            $(nowOpenedLi).remove();
            setEditingFalse();
        }
    });
    
    function lastClickedRowNotEqualsClickedRow(nowOpenedLi) {
        if($(lastClickedRowLi).attr('data-id') != $(nowOpenedLi).attr('data-id')) {
            $(lastClickedRowLi).find('li').trigger('click');            
        }
    }
    
    function closeActiveQuestionRow(buttonElement) {        
        $('.questionTableRow').removeAttr('data-opened');
        var containerLi = $(buttonElement).closest('li');            
        $(containerLi).find('#QuestionAnswerRow').html('').hide();
        
        $('.questionRowLi').find('span').removeClass('hoverSpan');                
        cancelAllInputShowSpan($(containerLi));
        unBindSpanClickEvent($(containerLi));
        setEditingFalse();
    }
    
    function unBindSpanClickEvent(containerLi) {        
        $(containerLi).find('span').filter(function(){return $(this).hasClass('preSpan');}).off();
    }
    
    function isQuestionDataEquals(cancelButton) {        
        var questionId = $(cancelButton).closest('li').attr('data-id');
        var questionTitle = $(cancelButton).closest('li').find('#title').val();
        var questionTitlePrevValue = $(cancelButton).closest('li').find('#titleSpan').attr('data-prev');
        var questionText = $(cancelButton).closest('li').find('#text').val();
        var questionTextPrevValue = $(cancelButton).closest('li').find('#textSpan').attr('data-prev');
        var questionType = $(cancelButton).closest('li').find('#questionType'+questionId).find('option:selected').text();
        var questionTypeId = $(cancelButton).closest('li').find('#questionType'+questionId).val();
        var questionTypePrevValue = $(cancelButton).closest('li').find('#questionTypeSpan').attr('data-prev');
        var questionTarget = $(cancelButton).closest('li').find('#targetType'+questionId).val();
        var questionTargetPrevValue = $(cancelButton).closest('li').find('#targetTypeSpan').attr('data-prev');
        if(questionTitle != questionTitlePrevValue || 
           questionText != questionTextPrevValue ||
           questionType != questionTypePrevValue ||
           questionTarget != questionTargetPrevValue) {                 
                return false;                
        }
        
        var answerNa = $(cancelButton).closest('li').find('#naInput').val();
        var answerNaPrevValue = $(cancelButton).closest('li').find('#naSpan').attr('data-prev');
        var answerNaAvailable = $(cancelButton).closest('li').find('#naAvailable').is(':checked');
        var answerNaAvailablePrevValue = $(cancelButton).closest('li').find('#naAvailable').attr('data-prev');                
        
        if((true == answerNaAvailable && 0 == answerNaAvailablePrevValue) ||
           (false == answerNaAvailable && 1 == answerNaAvailablePrevValue)) {            
            return false;
        }
        
        if(1 == questionTypeId) {
            return isQuestionAnswerDataTypeYesNoEquals(cancelButton,answerNa,answerNaPrevValue);            
        } else if(2 == questionTypeId) {
            return isQuestionAnswerDataTypeScaleEquals(cancelButton,answerNa,answerNaPrevValue);            
        }
        return true;
    }
    
    function isQuestionAnswerDataTypeYesNoEquals(cancelButton,answerNa,answerNaPrevValue) {
        var answerYes = $(cancelButton).closest('li').find('#yesInput').val();
        var answerYesPrevValue = $(cancelButton).closest('li').find('#yesSpan').attr('data-prev');
        var answerNo = $(cancelButton).closest('li').find('#noInput').val();
        var answerNoPrevValue = $(cancelButton).closest('li').find('#noSpan').attr('data-prev');            
        
        if(answerYes != answerYesPrevValue || 
           answerNo != answerNoPrevValue || 
           answerNa != answerNaPrevValue) {           
            return false;                
        }
        
        return true;
    }
    
    function isQuestionAnswerDataTypeScaleEquals(cancelButton,answerNa,answerNaPrevValue) {
        var answerOne = $(cancelButton).closest('li').find('#oneInput').val();
        var answerOnePrevValue = $(cancelButton).closest('li').find('#oneSpan').attr('data-prev');
        var answerTwo = $(cancelButton).closest('li').find('#twoInput').val();
        var answerTwoPrevValue = $(cancelButton).closest('li').find('#twoSpan').attr('data-prev');
        var answerThree = $(cancelButton).closest('li').find('#threeInput').val();
        var answerThreePrevValue = $(cancelButton).closest('li').find('#threeSpan').attr('data-prev');
        var answerFour = $(cancelButton).closest('li').find('#fourInput').val();
        var answerFourPrevValue = $(cancelButton).closest('li').find('#fourSpan').attr('data-prev');
        var answerFive = $(cancelButton).closest('li').find('#fiveInput').val();
        var answerFivePrevValue = $(cancelButton).closest('li').find('#fiveSpan').attr('data-prev');
        if('-' == answerTwoPrevValue) {
            answerTwoPrevValue = '';
        }
        if('-' == answerThreePrevValue) {
            answerThreePrevValue = '';
        }
        if('-' == answerFourPrevValue) {
            answerFourPrevValue = '';
        }
        if('-' == answerNaPrevValue) {
            answerNaPrevValue = '';
        }
        if(answerOne != answerOnePrevValue || 
           answerTwo != answerTwoPrevValue || 
           answerThree != answerThreePrevValue || 
           answerFour != answerFourPrevValue || 
           answerFive != answerFivePrevValue || 
           answerNa != answerNaPrevValue) {           
            return false;                
        }
        return true;
    }
    
    $(document).on('click','#saveQuestion',function() {         
        var questionId = $(this).closest('li').attr('data-id');
        if('undefined' == typeof(questionId)) {
            questionId = '';
        }
        
        var sequence = $(this).closest('li').find('#sequence').text();
        var questionTitle = $(this).closest('li').find('#title').val();
        var questionText = $(this).closest('li').find('#text').val();
        var questionType = $(this).closest('li').find('#questionType'+questionId).val();
        var questionTarget = $(this).closest('li').find('#targetType'+questionId).val();
        var containerLi = $(this).closest('li');
        var answerNa = $(this).closest('li').find('#naInput').val();
        var answerNaAvailable = $(this).closest('li').find('#naAvailable');
        var answerNaAvailableState = $(this).closest('li').find('#naAvailable').is(':checked');

        if ( isQuestionDataEquals($(this)) ) {
            closeActiveQuestionRow($(this));
            return false;
        }
        
        if (1 == questionType) {
            var answerYes = $(this).closest('li').find('#yesInput').val();
            var answerNo = $(this).closest('li').find('#noInput').val();  
            
            if ( checkQuestionDataTypeYesNo(questionTitle,questionText,questionType,questionTarget,answerYes,answerNo,answerNa,answerNaAvailable) ) {
                saveQuestionTypeYesNo(
                    sequence,
                    questionTitle,
                    questionText,
                    questionType,
                    questionTarget,
                    answerYes,
                    answerNo,
                    answerNa,
                    containerLi,
                    questionId,
                    answerNaAvailableState
                );
            }
            else {
                alert('Helytelen adatbevitel!');
            }
        }
        else if(2 == questionType) {
            var answerOne = $(this).closest('li').find('#oneInput').val();
            var answerTwo = $(this).closest('li').find('#twoInput').val();
            var answerThree = $(this).closest('li').find('#threeInput').val();
            var answerFour = $(this).closest('li').find('#fourInput').val();
            var answerFive = $(this).closest('li').find('#fiveInput').val();
            
            if (checkQuestionDataTypeScale(questionTitle,questionText,questionType,questionTarget,answerOne,answerFive,answerNa,answerNaAvailable)) {                
                    saveQuestionTypeScale(
                        sequence,
                        questionTitle,
                        questionText,
                        questionType,
                        questionTarget,
                        answerOne,
                        answerTwo,
                        answerThree,
                        answerFour,
                        answerFive,
                        answerNa,
                        containerLi,
                        questionId,
                        answerNaAvailableState
                    );
            }
            else {
                alert('Helytelen adatbevitel!');
            }
        }
        unBindSpanClickEvent(containerLi);
    });
    
    function saveQuestionTypeYesNo(sequence,questionTitle,questionText,questionType,questionTarget,answerYes,answerNo,answerNa,containerLi,questionId,answerNaAvailableState) {
        $.ajax({
            url: ajaxBaseUrl + "melysegi/mentes",
            type: 'POST',
            data: {
                questionId: questionId,
                sequence: sequence,
                rateableCollectionId: $('#rateableCollectionSelector').val(),
                questionTitle: questionTitle,
                questionText: questionText,
                questionType: questionType,
                questionTarget: questionTarget,
                answerYes: answerYes,
                answerNo: answerNo,
                answerNa: answerNa,
                answerNaAvailableState: answerNaAvailableState
            },
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            if (data.wasUpdated) {
                doChangesAfterUpdateQuestion(containerLi,questionId,data);
            }
            else {
                doChangesAfterSaveQuestion(containerLi,data,questionTitle,questionText,questionTarget);
            }
        });
    }
    
    function saveQuestionTypeScale(sequence,questionTitle,questionText,questionType,questionTarget,answerOne,answerTwo,answerThree,answerFour,answerFive,answerNa,containerLi,questionId,answerNaAvailableState) {
        $.ajax({
            url: ajaxBaseUrl + "melysegi/mentes",
            type: 'POST',
            data: {
                questionId: questionId,
                sequence: sequence,
                rateableCollectionId: $('#rateableCollectionSelector').val(),
                questionTitle: questionTitle,
                questionText: questionText,
                questionType: questionType,
                questionTarget: questionTarget,
                answerOne: answerOne,
                answerTwo: answerTwo,
                answerThree: answerThree,
                answerFour: answerFour,
                answerFive: answerFive,
                answerNa: answerNa,
                answerNaAvailableState: answerNaAvailableState                
            },
            dataType: 'JSON',
            async: false
        }).done(function(data) {
            if (data.wasUpdated) {
                doChangesAfterUpdateQuestion(containerLi,questionId,data);
            }
            else {
                doChangesAfterSaveQuestion(containerLi,data,questionTitle,questionText,questionTarget);
            }
        });
    }
    
    function doChangesAfterSaveQuestion(containerLi,data,questionTitle,questionText,questionTarget) {
        $(containerLi).find('#QuestionAnswerRow').html('').hide();
        $('li').removeAttr('data-opened');
        $(containerLi).attr('data-id',data.id);
        $(containerLi).find('#title').hide().parent().before('<span id="titleSpan" data-prev="'+questionTitle+'" class="preSpan">'+questionTitle+'</span>');
        $(containerLi).find('#text').hide().parent().before('<span id="textSpan" data-prev="'+questionText+'" class="preSpan">'+questionText+'</span>');
        var questionTypeValue = $(containerLi).find('#questionType').attr('id','questionType'+data.id).find('option:selected').text();
        $(containerLi).find('#questionType'+data.id).removeClass('chzn-done').before('<span id="questionTypeSpan" data-prev="'+questionTypeValue+'" class="preSpan">'+questionTypeValue+'</span>');
        $('#questionType_chzn').remove();
        $('#questionType'+data.id).chosen({disable_search_threshold: 10});
        $('#questionType'+data.id).prev().attr('data-answer-type-id',$('#questionType'+data.id).val());
        var targetTypeText = $(containerLi).find('#targetType').find('option:selected').text();
        $(containerLi).find('#targetType').removeClass('chzn-done').attr('id','targetType'+data.id).before('<span id="targetTypeSpan" data-prev="'+questionTarget+'" class="preSpan">'+targetTypeText+'</span>');
        $('#targetType_chzn').remove();
        $('#targetType'+data.id).chosen({disable_search_threshold: 10});
        $('.chzn-container').hide();
        $('.questionRowLi').find('span').filter(function(){return $(this).hasClass('preSpan');}).removeClass('hoverSpan');
        setEditingFalse();
    }
    
    function doChangesAfterUpdateQuestion(containerLi,questionId,data) {
        $(containerLi).find('#QuestionAnswerRow').html('').hide();        
        if(data.id == questionId) {
            hideAllInputShowSpan(containerLi);
        } else {            
            addNewQuestionRow(data.id,containerLi,questionId);
            cancelAllInputShowSpan(containerLi);
            sequenceChange();
        }
        $('.questionTableRow').removeAttr('data-opened');
        $('.questionRowLi').find('span').filter(function(){return $(this).hasClass('preSpan');}).removeClass('hoverSpan');
        setEditingFalse();
    }
    
    function addNewQuestionRow(newQuestionId,prevContainerLi,prevQuestionId) {
        var nextSequence = $( '#sortable li' ).filter(function(){return $(this).hasClass('questionRowLi');}).size()+1;
        var titleValue = $(prevContainerLi).find('#title').val();
        var textValue = $(prevContainerLi).find('#text').val();
        var questionTypeValue = $(prevContainerLi).find('#questionType'+prevQuestionId).find('option:selected').text();
        var questionTypeValueId = $(prevContainerLi).find('#questionType'+prevQuestionId).val();
        var targetTypeValue = $(prevContainerLi).find('#targetType'+prevQuestionId).val();
        var targetTypeText = $(prevContainerLi).find('#targetType'+prevQuestionId).find('option:selected').text();
        $.ajax({
            url: ajaxBaseUrl + "melysegi/kerdestipusok",
            type: 'POST',
            dataType: "json",            
            async: false
        }).done(function(data) {            
            var selectAnswerType = '';
            $.each(data,function() {
                selectAnswerType += '<option value="' + this.id + '">' + this.name + '</option>';                
            });          
            
            $('#sortable').append('\
                <li class="ui-state-default questionRowLi" data-id="'+newQuestionId+'">\n\
                    <div class="border"></div>\n\
                    <div class="questionTableRow">\n\
                        <div class="col col-1" id="sequence">' + nextSequence + '</div>\n\
                        <div class="col col-2">\n\
                            <span id="titleSpan" data-prev="' + titleValue + '" class="preSpan">' + titleValue + '</span>\n\
                            <div class="input-wrapper">\n\
                                <input id="title" class="titleInput" style="display:none;" type="text" value="' + titleValue + '" />\n\
                            </div>\n\
                        </div>\n\
                        <div class="col col-3">\n\
                            <span id="textSpan" data-prev="' + textValue + '" class="preSpan">' + textValue + '</span>\n\
                            <div class="input-wrapper">\n\
                                <input id="text" class="textInput" style="display:none;" type="text" value="' + textValue + '" />\n\
                            </div>\n\
                        </div>\n\
                        <div class="col col-4">\n\
                            <span id="questionTypeSpan" data-prev="' + questionTypeValue + '" class="preSpan" data-answer-type-id="' + questionTypeValueId + '">' + questionTypeValue + '</span>\n\
                            <select id="questionType'+newQuestionId+'" class="selectInput"  style="display:none;">\n\
                                ' + selectAnswerType +'\n\
                            </select>\n\
                        </div>\n\
                        <div class="col col-5">\n\
                            <span id="targetTypeSpan"  data-prev="' + targetTypeValue +'" class="preSpan">' + targetTypeText +'</span>\n\
                            <select id="targetType'+newQuestionId+'" class="selectInput"  style="display:none;">\n\
                                <option value="1">egyén</option>\n\
                                <option value="2">üzlet</option>\n\
                            </select>\n\
                        </div>\n\
                        <div class="col col-6"><div class="deleteBtn"></div></div>\n\
                    </div>\n\
                    <div id="QuestionAnswerRow"></div>\n\
                </li>');            
            $( "button.delete" ).button({
                icons: {
                  primary: "ui-icon-closethick"
                },
                text: false
            });
            $('#sortable li').last().find( '#questionType'+newQuestionId ).val(questionTypeValueId);
            $('#sortable li').last().find( '#targetType'+newQuestionId ).val(targetTypeValue);
            $('#sortable li').last().find( '.selectInput' ).chosen({disable_search_threshold: 10});   
            $('.chzn-container').hide();
        });
    }
    
    function hideAllInputShowSpan(containerLi) {  
        var questionId = $(containerLi).attr('data-id');
        var titleValue = $(containerLi).find('#title').val();
        var textValue = $(containerLi).find('#text').val();
        var questionTypeValue = $(containerLi).find('#questionType'+questionId).find('option:selected').text();
        var questionTypeValueId = $(containerLi).find('#questionType'+questionId).val();
        var targetTypeValue = $(containerLi).find('#targetType'+questionId).val();
        var targetTypeText = $(containerLi).find('#targetType'+questionId).find('option:selected').text();
        $(containerLi).find('#title').hide().parent().parent().find('.preSpan').attr('data-prev',titleValue).text(titleValue).show();
        $(containerLi).find('#text').hide().parent().parent().find('.preSpan').attr('data-prev',textValue).text(textValue).show();
        $(containerLi).find('#questionType'+questionId).prev().attr('data-answer-type-id',questionTypeValueId);
        $(containerLi).find('#questionType'+questionId+'_chzn').hide().prev().prev().attr('data-prev',questionTypeValue).text(questionTypeValue).show();
        $(containerLi).find('#targetType'+questionId+'_chzn').hide().prev().prev().attr('data-prev',targetTypeValue).text(targetTypeText).show();
    }
    
    function hideAllSpanWithoutSavePrevValue(containerLi) {
        var questionId = $(containerLi).attr('data-id');
        var questionTypeValue = $(containerLi).find('#questionType'+questionId).find('option:selected').text();        
        var targetTypeText = $(containerLi).find('#targetType'+questionId).find('option:selected').text();
        $(containerLi).find('#questionType'+questionId+'_chzn').hide().prev().prev().text(questionTypeValue).show();
        $(containerLi).find('#targetType'+questionId+'_chzn').hide().prev().prev().text(targetTypeText).show();
        $(containerLi).find('input[type=text]').each(function(){
            var that = $(this);            
            if($(that).parent().parent().find('span').hasClass('preSpan')) {
                if('' == $(that).val()) {                    
                    $(that).hide().parent().parent().find('.preSpan').text('-').show();                    
                } else {
                    $(that).hide().parent().parent().find('.preSpan').text($(that).val()).show();
                }
            }            
        });
    }
    
    function cancelAllInputShowSpan(containerLi) {  
        var questionId = $(containerLi).attr('data-id');        
        var titlePrevValue = $(containerLi).find('#titleSpan').attr('data-prev');
        var textPrevValue = $(containerLi).find('#textSpan').attr('data-prev');
        var questionTypePrevValue = $(containerLi).find('#questionTypeSpan').attr('data-prev');        
        var questionTypePrevValueId = $(containerLi).find('#questionTypeSpan').attr('data-answer-type-id');        
        var targetTypePrevValue = $(containerLi).find('#targetTypeSpan').attr('data-prev');
        var targetTypePrevText = $(containerLi).find('#targetTypeSpan').attr('data-prev-text');
        $(containerLi).find('#title').val(titlePrevValue).hide().parent().parent().find('.preSpan').text(titlePrevValue).show();
        $(containerLi).find('#text').val(textPrevValue).hide().parent().parent().find('.preSpan').text(textPrevValue).show();
        $(containerLi).find('#questionType'+questionId).val(questionTypePrevValueId).next().hide().prev().prev().text(questionTypePrevValue).show();
        $(containerLi).find('#targetType'+questionId).val(targetTypePrevValue).next().hide().prev().prev().text(targetTypePrevText).show();
        $('#questionType'+questionId).trigger("liszt:updated");
        $('#targetType'+questionId).trigger("liszt:updated");
    }
    
    function checkQuestionDataTypeYesNo(questionTitle,questionText,questionType,questionTarget,answerYes,answerNo,answerNa,answerNaAvailable) {
        if('' == questionTitle || '' == questionText || '' == questionType || '' == questionTarget || '' == answerYes || '' == answerNo) {
            return false;
        }
        if($(answerNaAvailable).is(':checked')) {
            if('' == answerNa) {
                return false;
            }
        }
        if(questionTitle.length > 20 || questionText.length > 256) {
            return false;
        }
        
        return true;
    }
    
    function checkQuestionDataTypeScale(questionTitle,questionText,questionType,questionTarget,answerOne,answerFive,answerNa,answerNaAvailable) {
        if('' == questionTitle || '' == questionText || '' == questionType || '' == questionTarget || '' == answerOne || '' == answerFive) {
            return false;
        }
        if($(answerNaAvailable).is(':checked')) {
            if('' == answerNa) {
                return false;
            }
        }
        if(questionTitle.length > 20 || questionText.length > 256) {
            return false;
        }
        
        return true;
    }
    
    function setEditingFalse() {        
        editing = false;       
        $('.questionRowLi').removeClass('ui-state-disabled');
        $('#sortable').sortable( 'enable' );
    }
    
    function setEditingTrue() {
        editing = true; 
        $('.questionRowLi').addClass('ui-state-disabled');
        $('#sortable').sortable( 'disable' );
    }
        
});
