<?php get_header(); ?>

	<div id="content" class="autosize">
		<div id="menu-left" class="menu">
			<ul>
				<?php
				$category_menu = category_menu(get_query_var('cat'));
				echo $category_menu;
				?>
			</ul>
		</div>
		
		<div id="photobook">

<?php 

	$category_posts = new WP_Query("cat=" . $current_cat . "&showposts=-1");

	if ($category_posts->have_posts()) : while ($category_posts->have_posts()) : $category_posts->the_post(); 

		$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
		$photo = $photo_db->getPublishedPhoto($post->ID);
	
		if(isset($photo)){
			echo $photo->generateImgTag('main', '');
		}
?>

	<div class='nav'>
		<a class="prev"><img src="<?php bloginfo('template_directory'); ?>/images/arrow_prev.png"/></a>
		<a class="next"><img src="<?php bloginfo('template_directory'); ?>/images/arrow_next.png"/></a>
	</div>

<?php endwhile; endif; ?>

		</div>

		<div id="menu-right" class="menu">
			<ul>
				<?php echo $category_menu; ?>
			</ul>
		</div>
	</div> <!-- #content -->

<?php get_footer() ?>