<?php get_header(); ?>

	<div id="content">

		<?php

		// Calculate offset of photos to fetch
		$offset_main = 0;
		if(isset(get_query_var('paged'))) $offset_main = (get_query_var('paged') - 1) * 19;
		
		// Display only 1 element, the one going in main frame

		$post_request = 'showposts=1&offset=' . $offset_main;
		if(is_category()) $post_request .= "&cat=".get_query_var('cat');
		query_posts($post_request);
		
		while (have_posts()) : the_post();
			
			global $page; $page = 1; ?>

				<div class="post">

					<div id="main_photo">
						<?php 
							// Just display photo
							echo ereg_replace( "<div.*<\/div>", "", get_the_content() );
						?>
					</div><!-- entry -->
					
					<h5 id="photo_title"><?php the_title(); ?></h5>
					
					<?php 
						// Just display description
						//echo ereg_replace( "<img.[^>]*>", "", get_the_content() );
					?>

				</div><!-- post -->
				
			<div id="navigator">

			<?php 
			
			// Display this post in navigator first
			// because other will be dynamicly assigned
			$excerpt = get_the_excerpt();
			$excerpt = ereg_replace( "nav_photo", "nav_photo selected_photo first_column", $excerpt );
			echo $excerpt;
			
			endwhile;
		?>
		<?php
		
		// Get the last 19 excerpt to display thumbnail
		$post_request = 'showposts=19&offset='. ($offset_main + 1);
		if(is_category()) $post_request .= "&cat=".get_query_var('cat');
		query_posts($post_request);
		$i = 1;

		while (have_posts()) : the_post();
		
			// If we reach last thumbnail, display next_page arrow instead of the last thumbnail
			if($i == 19){ ?>
			
				<a class="nav_photo" href="<?php next_posts(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow_next.png" width="65px" heigth="49px"></a> <?php
			
			} else {
				
				// Display previous_page arrow if we're not on page 1
				if(get_query_var('paged') > 1 and $i == 16) { ?>
					
					<a class="nav_photo" href="<?php previous_posts(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow_prev.png" width="65px" heigth="49px"></a><?php
					
				}
				else {
					$excerpt = get_the_excerpt();

					if($i == 0) $excerpt = ereg_replace( "nav_photo", "nav_photo selected_photo", $excerpt );

					// add new class without left margin
					if($i%4 == 0) $excerpt = ereg_replace( "nav_photo", "nav_photo first_column", $excerpt );
					$i += 1;

					echo $excerpt;
				}
			}
		endwhile;
		?>
		</div> <!-- navigator -->
		
	</div><!-- content -->

<?php get_footer(); ?>
