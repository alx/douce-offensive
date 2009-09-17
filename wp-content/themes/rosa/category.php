<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	echo PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID)->generateImgTag('main', '');
?>

<?php endwhile; endif; ?>

<div class="textset">
	<h1><?php single_cat_title(); ?></h1>
</div>

<div class="workset">
	<?php echo category_description(); ?>
</div>

<?php get_footer(); ?>
