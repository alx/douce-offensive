jQuery(document).ready(function() {
	
	var all_img_width = 460;
	
	jQuery("#photobook .photo").each(function() {
		all_img_width += (this.width + 50);
	})
	
	jQuery("#content").width(all_img_width);
	
	jQuery(".nav:last").remove();
	jQuery(".next:last").remove();
	
	jQuery(".next").click(function(){
		var nextImg = jQuery(this).parents('.nav')[0].nextElementSibling.nextElementSibling;
		jQuery('html, body').animate({scrollLeft: nextImg.offsetLeft}, 500);
	})
	
	jQuery(".prev").click(function(){
		var previousImg = jQuery(this).parents('.nav')[0].previousElementSibling.previousElementSibling;
		jQuery('html, body').animate({scrollLeft: previousImg.offsetLeft}, 500);
	})
    
});