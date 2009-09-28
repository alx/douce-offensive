jQuery(document).ready(function() {
	
	var all_img_width = 460;
	
	jQuery("#photobook .photo").each(function() {
		all_img_width += (this.width + 41);
	})
	
	jQuery("#content").width(all_img_width);
	
	jQuery(".nav:last").remove();
	
	jQuery(".next").click(function(){
		var imgOffset = jQuery(this).parents('.nav')[0].nextElementSibling.offsetLeft;
		jQuery('html,body').animate({scrollRight: imgOffset}, 500);
	})
	
	jQuery(".prev").click(function(){
		var imgOffset = jQuery(this).parents('.nav')[0].previousElementSibling.offsetLeft;
		jQuery('html,body').animate({scrollLeft: imgOffset}, 500);
	})
    
});