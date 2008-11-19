<?php get_header(); ?>

<?php get_sidebar(); ?>

	<div id="content">

		<?php if (have_posts()) :

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">

					<div class="main-photo">
						<?php the_content('<span class="nowrap">' . __('Read more', 'skimmed') . ' &raquo;</span>'); ?>
					</div><!-- entry -->
					
					<?php skimmed_milk_post_title('h2'); ?>

				</div><!-- post -->

			<?php endwhile;
			
		else : // no posts

			skimmed_milk_something_not_found(__('No posts found', 'skimmed'));

		endif; // end if have posts
		
		?>
		<div id="navigator">
		<?php
		// Get the last 10 posts in the special_cat category.
		query_posts('showposts=10');
		
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
