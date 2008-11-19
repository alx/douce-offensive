jQuery(document).ready(function() {
	jQuery(".nav_photo").click(function() {
		jQuery(this).attr("url");
		jQuery(".photoQcontent").attr("src", jQuery(this).attr("url"));
		jQuery("div.nav_photo").css("border","#000 1px solid");
	    jQuery(this).css("border","#fff 1px solid");
	});
});