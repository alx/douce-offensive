<?php get_header(); ?>

	<div id="content">

		<?php $quoted_searchterm = "&quot;" . attribute_escape(stripslashes($s)) . "&quot;";

		if (have_posts()) : ?>

			<h2 class="pagetitle"><?php printf(__('Posts containing %s', 'skimmed'), $quoted_searchterm); ?></h2>

			<?php skimmed_milk_nav_link(sprintf(__('Later posts containing %s', 'skimmed'), $quoted_searchterm), '');		

			while (have_posts()) : the_post();

				global $page; $page = 1; ?>

				<div class="post">
					<?php skimmed_milk_post_title(); ?>

					<div class="entry">
						<?php the_excerpt(); ?>
					</div><!-- entry -->

					<?php skimmed_milk_post_meta(); ?>

					<!--
					<?php trackback_rdf(); ?>
					-->
				</div><!-- post -->

			<?php endwhile;

			skimmed_milk_nav_link('', sprintf(__('Earlier posts containing %s', 'skimmed'), $quoted_searchterm));

		else : // no posts ?>

			<h2 class="pagetitle problem"><?php echo sprintf(__('No posts contain %s', 'skimmed'), $quoted_searchterm); ?></h2>

		<?php endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
