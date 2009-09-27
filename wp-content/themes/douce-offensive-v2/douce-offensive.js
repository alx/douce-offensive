jQuery(document).ready(function() {
	
	var all_img_width = 0;
	
	jQuery("#photobook img").each(function() {
		all_img_width += (this.width + 25);
	})
	
	jQuery("#content").width(all_img_width);
});