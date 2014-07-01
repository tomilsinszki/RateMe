$(document).ready(function(){

    function setupClickEventForRateables() {
        $('span.changeArchiveStatus').click(function() {
            var url = $(this).attr('data-url');
            var isActive = ($(this).attr('data-isactive')==1) ? 0 : 1;
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {isActive: isActive},
                dataType: "html",
                success: function (html) {
                    $("#rateablesContainer").html(html);
                    setupClickEventForRateables();
                }
            });
        });
    }
    setupClickEventForRateables();
    
});
