<?php get_header() ?>

	<div id="content" class="autosize">
		<div id="menu-left" class="menu">
			<ul>
				<?php
				$categories = get_categories();

				$category_output = '';
				$i = 1;
				foreach($categories as $cat):
					$category_output .= '<li><a title="' . sprintf(__( 'View all posts filed under %s' ), attribute_escape($cat->name)) . '"';
					$category_output .= ' href="' . get_category_link( $cat->term_id ) . '">' . attribute_escape($cat->name) . '</a>';
					$category_output .= ' (' . intval($cat->count) . ')</li>';
				endforeach;

				$category_output .= '<li>-- contact --</li><li><a href="mailto:globaleffect@gmail.com" class="email">globaleffect@gmail.com</a></li><ul>';
				echo $category_output;
				?>
			</ul>
		</div>
		
		<div id="photobook">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
	$photo = $photo_db->getPublishedPhoto($post->ID);
	
	if(isset($photo)){
		echo $photo->generateImgTag('main', '');
	}
?>

		<div class='nav'>
			<img src="<?php bloginfo('template_directory'); ?>/images/arrow_next.png" class="next" width="10px" height="15px"/>
			<img src="<?php bloginfo('template_directory'); ?>/images/arrow_prev.png" class="prev" width="10px" height="15px"/>
		</div>

<?php endwhile; endif; ?>

		</div>

		<div id="menu-right" class="menu">
			<ul>
				<?php echo $category_output; ?>
			</ul>
		</div>
	</div> <!-- #content -->

<?php get_footer() ?>