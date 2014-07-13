$(document).ready(function() {
	$(".nav-button").click(function() {
		$(".floated-nav").slideToggle(300);
		return false;
	});
	
	var scroll_offset = 80;
	
	$("header a").click(function(){
	    $("html, body").animate({
	        scrollTop: $($.attr(this, "href")).offset().top - scroll_offset
	    }, 1000);
	    
	    $(".floated-nav").hide();
	    
	    return false;
	});
});