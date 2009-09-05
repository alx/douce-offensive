<?php

function query_main_photo($offset = 0) {
	$post_request = 'showposts=1&offset=' . $offset;
	if(is_category()) $post_request .= "&cat=".get_query_var('cat');
	if(is_front_page()) $post_request .= '&orderby=date&order=DESC';
	return new WP_Query($post_request);
}

function query_thumbnails($offset = 0) {
	$post_request = 'showposts=19&offset='. ($offset + 1);
	if(is_category()) $post_request .= "&cat=".get_query_var('cat');
	if(is_front_page()) $post_request .= '&orderby=rand';
	return new WP_Query($post_request);
}

function douce_main($post) {
	$sizes = get_post_meta($post->ID, 'photoQImageSizes', true);
	
	$main = '<img class="photoQcontent" height="'.$sizes['main']['imgHeight'].'" ';
        $main = 'src="'.$sizes['main']['imgUrl'].'" alt="'.$post->title.'"/>';
	
	return $main;
}

function douce_thumbnail($post, $selected = false, $new_row = false) {
	$sizes = get_post_meta($post->ID, 'photoQImageSizes', true);
	
	// Preoload image
	$thumbnail = '<script type="text/javascript" charset="utf-8">';
        $thumbnail .= 'jQuery.preloadImages("'.$sizes['main']['imgUrl'].'");</script>';
	
	// Display image
	$thumbnail .= '<div class="thumbnail';
	
	// Add style depending on the row/selected position
	if($selected)
		$thumbnail .= ' selected_photo';
	
	if($new_row)
		$thumbnail .= ' first_column';
	
	// Close image
	$thumbnail .= '"><img width="'.$sizes['thumbnail']['imgWidth'].'" ';
        $thumbnail .= 'height="'.$sizes['thumbnail']['imgHeight'].'" alt="'.$post->title.'" ';
        $thumbnail .= 'src="'.$sizes['thumbnail']['imgUrl'].'" class="nav_photo photoQexcerpt photoQLinkImg" ';
        $thumbnail .= 'data-fullpath="'.$sizes['main']['imgUrl'].'"/></div>';
	
	// Return image
	return $thumbnail;
}


// Display only 1 element, the one going in main frame
function display_main_photo($offset = 0) {
	$main_photo = query_main_photo($offset);
	
	if ($main_photo->have_posts()) : $main_photo->the_post(); 
	
		global $post;
	?>

			<div class="post">

				<div id="main_photo">
					<?php 
						// Just display photo
						echo douce_main($post);
					?>
				</div><!-- entry -->
				
				<h5 id="photo_title"><?php the_title(); ?></h5>

			</div><!-- post -->
			
		<div id="navigator">

		<?php 
		
		// Display this post in navigator first
		// because other will be dynamicly assigned
		echo douce_thumbnail($post, true, true);
		
	endif;
}

function display_thumbnails($offset = 0, $current_page = 0) {
	$thumbnails = query_thumbnails($offset);
	$i = 1;
	
	// FIXME: should not be necessary,
	// small hack to not re-display main_thumbnail
	if(is_front_page())
		$thumbnails->the_post(); 
	
	while ($thumbnails->have_posts()) : $thumbnails->the_post();
		global $post;
		// If we reach last thumbnail, display next_page arrow instead of the last thumbnail
		if($i == 19 and !is_front_page()){ ?>
		
			<a class="thumbnail" href="<?php next_posts(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow_next.png" width="65px" heigth="49px"></a> <?php
		
		} else {
			
			// Display previous_page arrow if we're not on page 1
			if($current_page >= 1 and $i == 16) { ?>
				
				<a class="thumbnail first_column" href="<?php previous_posts(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow_prev.png" width="65px" heigth="49px"></a><?php
				
			}
			else {
				// Display thumnail with selected-photo and new_row style
				echo douce_thumbnail($post, ($i == 0), ($i%4 == 0));
			}
		}
		
		$i += 1;
	endwhile;
	
	// If needed, add missing cases until reacing previous_page link
	if($current_page >= 1 and $i < 16){
		
		$missing_cases = 16 - $i;
		
		for($j = 0; $j < $missing_cases; $j++) {
			
			// Dont forget to increment $i to take care of first_column border
			?> <div class="empty_thumb <?php if($i%4 == 0) echo "first_column"; ?>"></div> <?php
			$i += 1;
		}
		
		?><a class="thumbnail first_column" href="<?php previous_posts(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow_prev.png" width="65px" heigth="49px"></a><?php
	}
}


?>