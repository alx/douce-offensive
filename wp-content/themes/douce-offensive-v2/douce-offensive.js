jQuery(document).ready(function() {
	
	var all_img_width = 100;
	
	jQuery("#photobook img").each(function() {
		all_img_width += (this.width + 10);
	})
	
	jQuery("#content").width(all_img_width);
});