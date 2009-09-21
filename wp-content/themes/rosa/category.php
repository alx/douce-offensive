<?php 
	get_header();
	
	$current_cat = get_query_var('cat');
	$category_posts = new WP_Query("cat=" . $current_cat . "&showposts=-1");
?>

<div id="photobook" class="autosize">
	<div id="photolist">
		
<?php if ($category_posts->have_posts()) : while ($category_posts->have_posts()) : $category_posts->the_post(); ?>

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