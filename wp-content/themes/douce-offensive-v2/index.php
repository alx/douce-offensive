<?php get_header() ?>

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
			
			<div class='nav'>
				<a class="next"><img src="<?php bloginfo('template_directory'); ?>/images/arrow_next.png"/></a>
			</div>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
	$photo = $photo_db->getPublishedPhoto($post->ID);
	
	if(isset($photo)){
		echo $photo->generateImgTag('main', 'photo');
	}
?>

			<div class='nav'>
				<a class="prev"><img src="<?php bloginfo('template_directory'); ?>/images/arrow_prev.png"/></a>
				<a class="next"><img src="<?php bloginfo('template_directory'); ?>/images/arrow_next.png"/></a>
			</div>

<?php endwhile; endif; ?>

		</div>

	</div> <!-- #content -->

<?php get_footer() ?>