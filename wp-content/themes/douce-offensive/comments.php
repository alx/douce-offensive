<?php /* SECURITY CHECKS */

	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

        if ( ! empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
				return; // nothing to see
            }
        }
?>




<?php /* IF THERE ARE COMMENTS, THEN LIST THEM */

if ($comments) :

	$oddcomment = true; // toggle for alternating comment background ?>

	<a name="comments"></a>
	<h3 class="comments"><?php comments_number(__('No responses', 'skimmed'), __('One response', 'skimmed'), '% ' . __('responses', 'skimmed')); ?></h3>

	<ol class="commentlist">

	<?php foreach ($comments as $comment) : ?>

		<li id="comment-<?php comment_ID(); ?>"<?php
				$oddcomment = ! $oddcomment;
				if($comment->comment_approved == '0') {
					echo ' class="awaitingmoderation"';
				}
				if( $oddcomment ) {
					echo ' class="alt"';
				}?>><!-- end li open tag -->
			<?php if ($comment->comment_approved == '0') : ?>
				<p><strong><em><?php _e('Your comment is awaiting moderation', 'skimmed'); ?></em></strong></p>
			<?php endif; ?>
			<cite><?php comment_author_link(); ?></cite> <?php comment_type(__('comments', 'skimmed'), __('tracks back', 'skimmed'), __('pings back', 'skimmed')); ?>:
			<br />

			<small class="commentmetadata"><a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Link to this comment', 'skimmed'); ?>"><?php printf(__('%s at %s', 'skimmed'), get_comment_date(__('F jS, Y', 'skimmed')), get_comment_time('')); ?></a>
				<?php edit_comment_link(__('Edit', 'skimmed'),'<strong>|</strong> ',''); ?></small>

			<?php comment_text(); ?>

		</li>

	<?php endforeach; /* end for each comment */ ?>

	</ol><!-- commentlist -->

<?php endif; // end if $comments ?>




<div class="ui">
<?php /* IF POSSIBLE, ALLOW USER TO RESPOND */

if ('open' == $post->comment_status) : // if comments open ?>

	<a name="respond"></a>
	<h3 class="comments"><?php _e('Leave a comment', 'skimmed'); if(is_attachment()) echo ' ' . __('about this attachment', 'skimmed'); ?></h3>

	<?php if (get_option('comment_registration') && ! $user_ID) : // if you can't post ?>

		<p>
			<?php printf(__('You must be %slogged in%s to post a comment', 'skimmed'),
				'<a href="' . get_option('siteurl') . '/wp-login.php?redirect_to=' . urlencode(apply_filters('the_permalink', get_permalink())) . '" title="' . __('Log in here', 'skimmed') . '">', '</a>'); ?>
		</p>

	<?php else : // it's ok for you to post ?>

		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" name="commentform" id="commentform">

		<?php if ( ! $user_ID) : // if not logged in?>

			<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
			<label for="author"><small><?php _e('Name', 'skimmed'); if ($req) echo ' ' . __('(required)', 'skimmed'); ?></small></label></p>

			<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
			<label for="email"><small><?php _e("Mail (won't be published)", 'skimmed'); if ($req) echo ' ' . __('(required)', 'skimmed'); ?></small></label></p>

			<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
			<label for="url"><small><?php _e('Web site', 'skimmed'); ?></small></label></p>

		<?php endif; // end if not logged in ?>

		<p><textarea name="comment" id="comment" cols="90%" rows="10" tabindex="4"></textarea></p>

		<script type="text/javascript">
		<!--
			document.write(
			"<p id=\"submitcomment\"><a href=\"javascript:document.commentform.submit();\" title=\"<?php _e('Send your comment to ', 'skimmed'); bloginfo('name'); ?>\"><?php _e('Submit comment', 'skimmed'); ?> &raquo;</a></p>");
		//-->
		</script>
		<noscript>
			<p><input type="submit" name="submitcomment" id="submitcomment" tabindex="5" value="<?php _e('Submit comment', 'skimmed'); ?>" /></p>
		</noscript>

		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />

		<?php do_action('comment_form', $post->ID); ?>

		</form>

	<?php endif; // end if you can't post ?>

<?php else : // comments not open

	if ($comments) : // if are some comments (else just remain quiet) ?>
		<p class="nocomments"><?php _e('Comments are now closed', 'skimmed'); ?></p>
	<?php endif; // end if are comments ?>

<?php endif; // end if comments open ?>
</div><!-- ui -->