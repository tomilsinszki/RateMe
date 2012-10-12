$(document).ready(function(){
    $("#star0").click(function() {
        $("#star0").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star1").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star2").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star3").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star4").attr("src", "/bundles/acmerating/images/star_empty_150px.png");

        $("#stars").attr("value", "1");
    });

    $("#star1").click(function() {
        $("#star0").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star1").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star2").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star3").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star4").attr("src", "/bundles/acmerating/images/star_empty_150px.png");

        $("#stars").attr("value", "2");
    });

    $("#star2").click(function() {
        $("#star0").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star1").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star2").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star3").attr("src", "/bundles/acmerating/images/star_empty_150px.png");
        $("#star4").attr("src", "/bundles/acmerating/images/star_empty_150px.png");

        $("#stars").attr("value", "3");
    });

    $("#star3").click(function() {
        $("#star0").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star1").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star2").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star3").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star4").attr("src", "/bundles/acmerating/images/star_empty_150px.png");

        $("#stars").attr("value", "4");
    });

    $("#star4").click(function() {
        $("#star0").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star1").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star2").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star3").attr("src", "/bundles/acmerating/images/star_full_150px.png");
        $("#star4").attr("src", "/bundles/acmerating/images/star_full_150px.png");

        $("#stars").attr("value", "5");
    });
});
