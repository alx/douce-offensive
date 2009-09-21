<?php 
	get_header();
	
	$current_cat = get_query_var('cat');
	$category_posts = new WP_Query("cat=" . $current_cat . "&showposts=-1");
?>

	<div id="content" class="autosize">
		<div id="menu-left" class="menu">
			<ul>
				<?php
				$categories = get_categories();
				
				$category_output = '';
				$i = 1;
				foreach($categories as $cat):
					$category_output .= '<li><a title="' . sprintf(__( 'View all posts filed under %s' ), attribute_escape($cat->name)) . '"';
					if ($cat->cat_ID == $current_cat) {
						$category_output .= ' href="' . get_category_link( $cat->term_id ) . '"><strong>' . attribute_escape($cat->name) . '</strong></a>';
					} else {
						$category_output .= ' href="' . get_category_link( $cat->term_id ) . '">' . attribute_escape($cat->name) . '</a>';
					}
					$category_output .= ' (' . intval($cat->count) . ')</li>';
				endforeach;

				$category_output .= '<li>contact: <a href="mailto:globaleffect@gmail.com">globaleffect@gmail.com</a></li><ul>';
				echo $category_output;
				?>
			</ul>
		</div>
		
		<div id="photobook">

<?php if ($category_posts->have_posts()) : while ($category_posts->have_posts()) : $category_posts->the_post(); ?>

<?php
	$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
	$photo = $photo_db->getPublishedPhoto($post->ID);
	
	if(isset($photo)){
		echo $photo->generateImgTag('main', '');
	}
?>

<?php endwhile; endif; ?>

		</div>

		<div id="menu-right" class="menu">
			<ul>
				<?php echo $category_output; ?>
			</ul>
		</div>
	</div> <!-- #content -->

<?php get_footer() ?>