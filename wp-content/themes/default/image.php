<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

	<div id="content" class="widecolumn">

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<h2><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h2>
			<div class="entry">
				<p class="attachment"><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a></p>
				<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?></div>

				<?php the_content('<p class="serif">Lire la suite de cette entr�e &raquo;</p>'); ?>

				<div class="navigation">
					<div class="alignleft"><?php previous_image_link() ?></div>
					<div class="alignright"><?php next_image_link() ?></div>
				</div>
				<br class="clear" />

				<p class="postmetadata alt">
					<small>
						Cette entr�e a �t� publi�e le <?php the_time('l j F Y') ?> � <?php the_time() ?>
						et est class�e dans <?php the_category(', ') ?>.
						<?php the_taxonomies(); ?>
						Vous pouvez en suivre les commentaires par le biais du flux  <?php post_comments_feed_link('RSS 2.0'); ?>.

						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Both Comments and Pings are open ?>
							Vous pouvez  <a href="#respond">laisser un commentaire</a>, ou <a href="<?php trackback_url(); ?>" rel="trackback">faire un trackback</a> depuis votre propre site.

						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Only Pings are Open ?>
							Les commentaires sont ferm�s, mais vous pouvez  <a href="<?php trackback_url(); ?> " rel="trackback">faire un trackback</a> depuis votre propre site.

						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Comments are open, Pings are not ?>
							Vous pouvez aller directement � la fin et laisser un commentaire. Les pings ne sont pas autoris�s.

						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Neither Comments, nor Pings are open ?>
							Les commentaires et pings sont ferm�s.

						<?php } edit_post_link('Modifier cette entr�e.','',''); ?>

					</small>
				</p>

			</div>

		</div>

	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>D�sol�, aucun fichier ne correspond � vos crit�res.</p>

<?php endif; ?>

	</div>

<?php get_footer(); ?>
