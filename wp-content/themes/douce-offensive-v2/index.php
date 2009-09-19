<?php get_header() ?>

<?php while ( have_posts() ) : the_post() ?>

	<?php
		echo PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID)->generateImgTag('main', '');
	?>
			
<?php endwhile; ?>

<?php get_footer() ?>