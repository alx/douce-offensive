jQuery(document).ready(function() {
	
	var all_img_width = 150;
	
	jQuery("#photobook img").each(function() {
		all_img_width += (this.width + 30);
	})
	
	jQuery("#content").width(all_img_width);
	
	jQuery(".nav:last").hide();
	jQuery("#content").scrollable({
		items: '#photobook'
	});
});