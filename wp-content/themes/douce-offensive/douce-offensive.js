jQuery(document).ready(function() {
	jQuery(".nav_photo").click(function() {
		jQuery(this).attr("url");
		jQuery(".photoQcontent").attr("src", jQuery(this).attr("url"));
		jQuery("#photo_title").html(jQuery(this).children()[0].attr("alt"));
		jQuery("div.nav_photo").css("border","#000 1px solid");
	    jQuery(this).css("border","#fff 1px solid");
	});
});

jQuery.preloadImages = function() {
  for(var i = 0; i<arguments.length; i++) {
    jQuery("<img>").attr("src", arguments[i]);
  }
}