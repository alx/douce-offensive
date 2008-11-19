<?php get_header(); ?>

	<div id="content">

		<?php if (have_posts()) :

			skimmed_milk_nav_link('', '');

			the_post();

			// ** Note that this line hardwires maximum width and height in pixels for images on the attachment page **/
			$attachment_link = get_the_attachment_link($post->ID, true, array(500, 1000));
			// Arrange iconclass so could add style for narrow icons specially if desired
			$_post = &get_post($post->ID);
			// The iconsize field was set by get_the_attachment_link above
			$classname = $_post->iconsize[0] <= 128 ? 'smallattachment' : 'attachment';

			$mimetype = explode('/', $post->post_mime_type);
		?>
			<div class="post">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"
					title="<?php _e('To post containing this attachment', 'skimmed'); ?>"><?php echo get_the_title($post->post_parent); ?></a>
					&raquo;
					<a href="<?php echo get_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent link to this attachment page', 'skimmed'); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time(__('F jS, Y', 'skimmed')); ?></small>

				<div class="entry">
					<?php if ('audio' == $mimetype[0]) : ?>
						<p class="audio_attachment"><embed  class="audio_object" src="<?php echo wp_get_attachment_url($_post->ID); ?>" autostart="false"></embed>
					<?php elseif ('video' == $mimetype[0]) : ?>
						<p class="video_attachment"><embed  class="video_object" src="<?php echo wp_get_attachment_url($_post->ID); ?>" controller="true" autoplay="false"></embed>
					<?php else : ?>
						<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?>
					<?php endif; ?>

					<?php if ('image' != $mimetype[0]) : ?>
						<br />
						<a href="<?php echo wp_get_attachment_url($_post->ID); ?>" title="<?php printf(__('To the %2$s %1$s file', 'skimmed'), $mimetype[0], $mimetype[1]); ?>"><?php echo basename($post->guid); ?></a>
					<?php endif; ?>

					</p>

					<?php the_content(); // display the attachment description ?>

				</div><!-- entry -->

				<p class="postmetadata alt">
					<?php $dateString = __('F jS, Y', 'skimmed');
						printf(__('Uploaded on %1$s at %2$s', 'skimmed'),
							apply_filters('the_time', get_the_time($dateString), $dateString),
							apply_filters('the_time', get_the_time(''), ''));
					if ('open' == $post->comment_status) : // comments open
						echo '<br />';
						printf(__('%sFeed%s on comments about this attachment', 'skimmed'),
							'<a href="' . comments_rss() . '" title="' . __('RSS feed for comments about this attachment', 'skimmed') . '">',
							'</a>');
					endif; // end if comments open ?>
				</p>

			</div><!-- post -->

			<?php comments_template();

		else : // no posts

			skimmed_milk_something_not_found(__('Attachment not found', 'skimmed'));

		endif; // end if have posts ?>

	</div><!-- content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
