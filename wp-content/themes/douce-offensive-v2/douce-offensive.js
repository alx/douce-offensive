jQuery(document).ready(function() {
	
	var all_img_width = 460;
	
	jQuery("#photobook .photo").each(function() {
		all_img_width += (this.width + 41);
	})
	
	jQuery("#content").width(all_img_width);
	
	jQuery(".nav:last").remove();
	jQuery("#content").scrollable({
		items: '#photobook'
	});
});