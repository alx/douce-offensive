<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">

					<div class="main-photo">
						<?php 
							// Just display photo
							echo ereg_replace( "<p.*<\/p>", "", get_the_content() );
						?>
					</div><!-- entry -->
					
					<?php skimmed_milk_post_title('h2'); ?>
					
					
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
		// Get the last 20 posts in the special_cat category.
		query_posts('showposts=20');
		
		while (have_posts()) : the_post();
			?>
			<div class="thumbnail">
			<?php
			the_excerpt();
			?>
			</div>
			<?php
		endwhile;
		?>
		</div> <!-- navigator -->
		
	</div><!-- content -->

<?php get_footer(); ?>
