<?php get_header() ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	if($photo = PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID))
		echo $photo->generateImgTag('main', '');
?>

<?php endwhile; endif; ?>

<?php get_footer() ?>