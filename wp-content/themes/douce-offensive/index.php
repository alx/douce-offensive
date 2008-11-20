<?php get_header(); ?>

	<div id="content">

		<?php
			while (have_posts()) : the_post();
			
				global $page; $page = 1; ?>

				<div class="post">

					<div id="main_photo">
						<?php 
							// Just display photo
							echo ereg_replace( "<p.*<\/p>", "", get_the_content() );
						?>
					</div><!-- entry -->
					
					<h5 id="photo_title"><?php the_title(); ?></h5>
					
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
		$post_request = 'showposts=19&offset=1';

		if(is_category()) $post_request .= "";
		
		query_posts($post_request);
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
