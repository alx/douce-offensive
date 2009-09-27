jQuery(document).ready(function() {
	
	var all_img_width = jQuery("#menu-left").size + jQuery("#menu-right").size;
	
	jQuery("#photobook img").each(function() {
		all_img_width += (this.width + 40);
	})
	
	jQuery("#content").width(all_img_width);
});