<?php get_header(); ?>

<div id="photobook" class="autosize">
	<div id="photolist">
		
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	echo PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID)->generateImgTag('main', '');
?>

<?php endwhile; endif; ?>
	</div>
</div>

<div class="textset">
	<h1><?php echo $last_category->name; ?></h1>
</div>

<div class="workset">
	<p><?php echo $last_category->description; ?></p>
</div>

<?php get_footer(); ?>