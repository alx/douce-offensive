<?php get_header() ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
	$photo = $photo_db->getPublishedPhoto($post->ID);
	
	if(isset($photo)){
		echo $photo->generateImgTag('main', '');
	}
?>

<?php endwhile; endif; ?>

<?php get_footer() ?>