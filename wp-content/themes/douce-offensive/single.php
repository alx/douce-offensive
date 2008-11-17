<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) : ?>

			<div class="navigation">
				<div class="alignleft"><?php next_post_link('&laquo; %link'); ?></div>
				<div class="alignright"><?php previous_post_link('%link &raquo;'); ?></div>
				<div class="snap-to-fit"></div>
			</div><!-- navigation -->

			<?php the_post(); ?>

			<div class="post">
				<?php skimmed_milk_post_title('h2'); ?>

				<div class="entry">
					<?php skimmed_milk_post_entry(); ?>
				</div><!-- entry -->

				<p class="postmetadata alt">
					<?php
						$dateString = __('F jS, Y', 'skimmed');
						printf(__('Posted in %1$s on %2$s at %3$s', 'skimmed'),
							get_the_category_list(', '), 
							apply_filters('the_time', get_the_time($dateString), $dateString),
							apply_filters('the_time', get_the_time(''), ''));
							
						the_tags('<br />' . __('Tagged with ', 'skimmed'), ', ', ' ');

						$comments_open = 'open' == $post->comment_status;
						$trackbacks_allowed = 'open' == $post->ping_status;

						if($comments_open || $trackbacks_allowed)
						 	echo '<br />';

						skimmed_milk_post_comment_or_ping($comments_open, $trackbacks_allowed);
					?>
				</p>

				<!--
				<?php trackback_rdf(); ?>
				-->
			</div><!-- post -->

			<?php comments_template();

		else : // no posts

			skimmed_milk_something_not_found(__('Post not found', 'skimmed'));

		endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
