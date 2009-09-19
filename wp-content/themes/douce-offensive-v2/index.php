<?php get_header() ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	echo PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID)->generateImgTag('main', '');
?>

<?php endwhile; endif; ?>

<?php get_footer() ?>