<?php get_header(); ?>

<a class="prevPage browse left disabled"/>

<div id="wheeled" class="scrollable">
	<div class="items">
<?php
	
	$last_categories = get_categories(array(
		'orderby' => 'date',
		'order' => 'DESC',
		'hide_empty' => 1,
		'child_of' => 0,
		'current_category' => 0,
		'hierarchical' => true,
		'depth' => 0
	));
	$last_category = $last_categories[0];
?>

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
	<h1><?php echo $last_category->name; ?></h1>
</div>

<div class="workset">
	<p><?php echo $last_category->description; ?></p>
</div>

<?php get_footer(); ?>