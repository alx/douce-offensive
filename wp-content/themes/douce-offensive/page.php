<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			the_post(); ?>

			<h2 id="post-<?php the_ID(); ?>" class="pagetitle"><?php the_title(); ?></h2>

			<div class="post">
				<div class="entry">
					<?php skimmed_milk_post_entry(); ?>
				</div><!-- entry -->

				<?php 
					$comments_open = 'open' == $post->comment_status;
					$trackbacks_allowed = 'open' == $post->ping_status;

					if ($comments_open || $trackbacks_allowed || current_user_can('edit_page', $post->ID)) :
					// show metadata here as long as it isn't blank
				?>
						<p class="postmetadata alt">
							<?php skimmed_milk_post_comment_or_ping($comments_open, $trackbacks_allowed, $comments_open || $trackbacks_allowed); ?>
						</p>
				<?php endif; // end show metadata ?>

				<!--
				<?php trackback_rdf(); ?>
				-->
			</div><!-- post -->

			<?php comments_template();

		else : // no posts

			skimmed_milk_something_not_found(__('Page not found', 'skimmed'));

		endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
