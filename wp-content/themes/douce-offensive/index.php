<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">

					<div id="main_photo">
						<?php 
							// Just display photo
							echo ereg_replace( "<p.*<\/p>", "", get_the_content() );
						?>
					</div><!-- entry -->
					
					<?php skimmed_milk_post_title('h5'); ?>
					
					
					<?php 
						// Just display description
						echo ereg_replace( "<img.[^>]*>", "", get_the_content() );
					?>

				</div><!-- post -->

			<?php endwhile;
			
		else : // no posts

			skimmed_milk_something_not_found(__('No posts found', 'skimmed'));

		endif; // end if have posts
		
		?>
		<div id="navigator">
		<?php

		// Get the last 20 excerpt to display thumbnail
		query_posts('showposts=20');
		$i = 0;

		while (have_posts()) : the_post();
			$excerpt = get_the_excerpt();

			if($i == 0) $excerpt = ereg_replace( "nav_photo", "nav_photo selected_photo", $excerpt );
			
			// add new class without left margin
			if($i%4 == 0) $excerpt = ereg_replace( "nav_photo", "nav_photo first_column", $excerpt );
			$i += 1;
			
			echo $excerpt;
		endwhile;
		?>
		</div> <!-- navigator -->
		
	</div><!-- content -->

<?php get_footer(); ?>
