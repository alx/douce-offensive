<?php get_header(); ?>

<a class="prevPage browse left disabled"/>

<div id="wheeled" class="scrollable">
	<div class="items">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	echo PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID)->generateImgTag('main', '');
?>

<?php endwhile; endif; ?>
	</div>
</div>

<a class="nextPage browse right"/>
<br clear="all"/>

<script>
$(document).ready(function() {

	// initialize scrollable together with the mousewheel plugin
	$("#wheeled").scrollable().mousewheel();	
});
</script>

<div class="textset">
	<h1><?php single_cat_title(); ?></h1>
</div>

<div class="workset">
	<?php echo category_description(); ?>
</div>

<?php get_footer(); ?>
