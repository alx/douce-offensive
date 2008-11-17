<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			skimmed_milk_nav_link(__('Later posts', 'skimmed'), '');

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">
					<?php skimmed_milk_post_title('h2'); ?>

					<div class="entry">
						<?php the_content('<span class="nowrap">' . __('Read more', 'skimmed') . ' &raquo;</span>'); ?>
					</div><!-- entry -->

					<?php skimmed_milk_post_meta(); ?>

					<!--
					<?php trackback_rdf(); ?>
					-->
				</div><!-- post -->

			<?php endwhile;

			skimmed_milk_nav_link('', __('Earlier posts', 'skimmed'));

		else : // no posts

			skimmed_milk_something_not_found(__('No posts found', 'skimmed'));

		endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
