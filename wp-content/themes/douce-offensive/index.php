<?php get_header(); ?>

	<div id="content">

		<?php
			$exclude_post = 0;
			
			while (have_posts()) : the_post();
				$exclude_post = get_the_ID();
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
		query_posts('showposts=19&p=-'.$exclude_post);
		$i = 1;

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
