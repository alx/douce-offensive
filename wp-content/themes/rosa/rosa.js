jQuery(document).ready(function() {
	
	all_img_width = 0;
	
	jQuery("#photolist img").each(function() {
		all_img_width += (this.width + 10);
	})
	
	jQuery("#photobook").width(all_img_width);
});