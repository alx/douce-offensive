jQuery(document).ready(function() {
	
	var all_img_width = 460;
	
	jQuery("#photobook .photo").each(function() {
		all_img_width += (this.width + 41);
	})
	
	jQuery("#content").width(all_img_width);
	
	jQuery(".nav:last").remove();
	
	jQuery(".next").click(function(){
		var divOffset = jQuery('#photobook').offset().left;
		var navOffset = jQuery(this).parents('.nav')[0].nextElementSibling.offsetLeft;;
		var imgScroll = navOffset - divOffset;
		jQuery('#photobook').animate({scrollLeft: '+=' + imgScroll + 'px'}, 500);
	})
	
	jQuery(".prev").click(function(){
		var divOffset = jQuery('#photobook').offset().left;
		var navOffset = jQuery(this).parents('.nav')[0].previousElementSibling.offsetLeft;
		var imgScroll = navOffset - divOffset;
		jQuery('#photobook').animate({scrollLeft: '+=' + imgScroll + 'px'}, 500);
	})
});