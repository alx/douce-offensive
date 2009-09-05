<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	echo $photoq->_db->getPublishedPhoto($post->ID)->generateImgTag('main');
?>

<?php endwhile; endif; ?>

<div class="textset">
	<h1></h1>
</div>

<div class="workset">
	<p></p>
</div>

<?php get_footer(); ?>
