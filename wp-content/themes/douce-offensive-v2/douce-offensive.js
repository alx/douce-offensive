jQuery(document).ready(function() {
	
	all_img_width = 0;
	
	jQuery("#photobook img").each(function() {
		all_img_width += (this.width + 10);
	})
	
	jQuery("#content").width(all_img_width);
});