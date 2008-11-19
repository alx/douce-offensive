<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">
					<?php skimmed_milk_post_title('h2'); ?>

					<div class="main-photo">
						<?php the_content('<span class="nowrap">' . __('Read more', 'skimmed') . ' &raquo;</span>'); ?>
					</div><!-- entry -->

				</div><!-- post -->

			<?php endwhile;

			skimmed_milk_nav_link('', __('Earlier posts', 'skimmed'));

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

<?php get_sidebar(); ?>

<?php get_footer(); ?>
