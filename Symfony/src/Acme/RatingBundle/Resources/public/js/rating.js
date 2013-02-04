$(document).ready(function(){
    $("#star0").click(function() {
        $("#star0").addClass("on");
        $("#star1").removeClass("on");
        $("#star2").removeClass("on");
        $("#star3").removeClass("on");
        $("#star4").removeClass("on");

        $("#stars").attr("value", "1");
        $("#submitRating").attr("disabled", false);
    });

    $("#star1").click(function() {
        $("#star0").addClass("on");
        $("#star1").addClass("on");
        $("#star2").removeClass("on");
        $("#star3").removeClass("on");
        $("#star4").removeClass("on");

        $("#stars").attr("value", "2");
        $("#submitRating").attr("disabled", false);
    });

    $("#star2").click(function() {
        $("#star0").addClass("on");
        $("#star1").addClass("on");
        $("#star2").addClass("on");
        $("#star3").removeClass("on");
        $("#star4").removeClass("on");

        $("#stars").attr("value", "3");
        $("#submitRating").attr("disabled", false);
    });

    $("#star3").click(function() {
        $("#star0").addClass("on");
        $("#star1").addClass("on");
        $("#star2").addClass("on");
        $("#star3").addClass("on");
        $("#star4").removeClass("on");

        $("#stars").attr("value", "4");
        $("#submitRating").attr("disabled", false);
    });
    
    $("#star4").click(function() {
        $("#star0").addClass("on");
        $("#star1").addClass("on");
        $("#star2").addClass("on");
        $("#star3").addClass("on");
        $("#star4").addClass("on");
        
        $("#stars").attr("value", "5");
        $("#submitRating").attr("disabled", false);
    });
});
