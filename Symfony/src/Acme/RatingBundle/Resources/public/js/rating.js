$(document).ready(function(){
    $("#star0").click(function() {
        $("#star0").attr("src", "/images/star_full_150px.png");
        $("#star1").attr("src", "/images/star_empty_150px.png");
        $("#star2").attr("src", "/images/star_empty_150px.png");
        $("#star3").attr("src", "/images/star_empty_150px.png");
        $("#star4").attr("src", "/images/star_empty_150px.png");

        $("#stars").attr("value", "1");
        $("#submitRating").attr("disabled", false);
    });

    $("#star1").click(function() {
        $("#star0").attr("src", "/images/star_full_150px.png");
        $("#star1").attr("src", "/images/star_full_150px.png");
        $("#star2").attr("src", "/images/star_empty_150px.png");
        $("#star3").attr("src", "/images/star_empty_150px.png");
        $("#star4").attr("src", "/images/star_empty_150px.png");

        $("#stars").attr("value", "2");
        $("#submitRating").attr("disabled", false);
    });

    $("#star2").click(function() {
        $("#star0").attr("src", "/images/star_full_150px.png");
        $("#star1").attr("src", "/images/star_full_150px.png");
        $("#star2").attr("src", "/images/star_full_150px.png");
        $("#star3").attr("src", "/images/star_empty_150px.png");
        $("#star4").attr("src", "/images/star_empty_150px.png");

        $("#stars").attr("value", "3");
        $("#submitRating").attr("disabled", false);
    });

    $("#star3").click(function() {
        $("#star0").attr("src", "/images/star_full_150px.png");
        $("#star1").attr("src", "/images/star_full_150px.png");
        $("#star2").attr("src", "/images/star_full_150px.png");
        $("#star3").attr("src", "/images/star_full_150px.png");
        $("#star4").attr("src", "/images/star_empty_150px.png");

        $("#stars").attr("value", "4");
        $("#submitRating").attr("disabled", false);
    });

    $("#star4").click(function() {
        $("#star0").attr("src", "/images/star_full_150px.png");
        $("#star1").attr("src", "/images/star_full_150px.png");
        $("#star2").attr("src", "/images/star_full_150px.png");
        $("#star3").attr("src", "/images/star_full_150px.png");
        $("#star4").attr("src", "/images/star_full_150px.png");

        $("#stars").attr("value", "5");
        $("#submitRating").attr("disabled", false);
    });
});
