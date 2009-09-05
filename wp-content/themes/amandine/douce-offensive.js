jQuery(document).ready(function() {
	jQuery(".thumbnail").click(function() {
		jQuery(".photoQcontent").attr("src", jQuery("img", this).attr("data-fullpath"));
		jQuery("#photo_title").html(jQuery("img", this).attr("alt"));
		jQuery(".thumbnail").css("border","#000 1px solid");
	    jQuery(this).css("border","#fff 1px solid");
	});
});

jQuery.preloadImages = function() {
  for(var i = 0; i<arguments.length; i++) {
    jQuery("<img>").attr("src", arguments[i]);
  }
}