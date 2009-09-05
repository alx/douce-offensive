<?php get_header(); ?>

	<div id="content">

		<?php

		// Calculate offset of photos to fetch
		$offset_main = 0;
		$current_page = get_query_var('paged') - 1;
		if($current_page == 1) {
			$offset_main = 19;
		}
		elseif ($current_page > 1) {
			$offset_main = 19 + (($current_page - 1) * 18);
		}
		
		display_main_photo($offset_main);
		
		// Get the last 19 excerpt to display thumbnail
		display_thumbnails($offset_main, $current_page);
		
		?>
		</div> <!-- navigator -->
		
	</div><!-- content -->

<?php get_footer(); ?>
