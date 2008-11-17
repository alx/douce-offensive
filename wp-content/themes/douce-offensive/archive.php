<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			if (is_category()) $archive_display_name = '<em>' . single_cat_title('', false) . '</em>';
			elseif (is_tag()) $archive_display_name = '<em>' . single_tag_title('', false) . '</em>';
			elseif (is_day()) $archive_display_name = get_the_time(__('F jS, Y', 'skimmed'));
			elseif (is_month()) $archive_display_name = get_the_time(__('F Y', 'skimmed'));
			elseif (is_year()) $archive_display_name = get_the_time(__('Y', 'skimmed'));
		?>

			<?php if (is_category()) : ?>
				<h2 class="pagetitle"><?php printf(__('Posts about %s', 'skimmed'),  $archive_display_name); ?></h2>
			<?php elseif (is_tag()) : ?>
				<h2 class="pagetitle"><?php printf(__('Posts tagged with %s', 'skimmed'),  $archive_display_name); ?></h2>
			<?php else : ?>
				<h2 class="pagetitle"><?php printf(__('%s Archive', 'skimmed'), $archive_display_name); ?></h2>
			<?php endif;

			skimmed_milk_nav_link(sprintf(__('Later %s posts', 'skimmed'), $archive_display_name), '');

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">
					<?php skimmed_milk_post_title(); ?>

					<?php if( ! is_year()) : // don't show content if is full year archive ?>
						<div class="entry">
							<?php the_content('<span class="nowrap">' . __('Read more', 'skimmed') . ' &raquo;</span>'); ?>
						</div><!-- entry -->
					<?php endif; ?>

					<?php skimmed_milk_post_meta(); ?>

					<!--
					<?php trackback_rdf(); ?>
					-->
				</div><!-- post -->

			<?php endwhile;

			skimmed_milk_nav_link('', sprintf(__('Earlier %s posts', 'skimmed'), $archive_display_name));

		else : // no posts

			skimmed_milk_something_not_found(__('Archive not found', 'skimmed'));

		endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
