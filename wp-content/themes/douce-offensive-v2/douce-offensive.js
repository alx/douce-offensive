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
	
	jQuery(".next").click(function(){
		var divOffset = $('#photobook').offset().left;
		var navOffset = $(this).parents('.nav')[0].nextElementSibling.offset().left;;
		var imgScroll = navOffset - divOffset;
		$('#photobook').animate({scrollLeft: '+=' + imgScroll + 'px'}, 500);
	})
	
	jQuery(".prev").click(function(){
		var divOffset = $('#photobook').offset().left;
		var navOffset = $(this).parents('.nav')[0].previousElementSibling.offset().left;
		var imgScroll = navOffset - divOffset;
		$('#photobook').animate({scrollLeft: '+=' + imgScroll + 'px'}, 500);
	})
});