<?php get_header() ?>

<div id="container">
	<div id="content">
		<div id="left-menu" class="menu">
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

				$category_output .= '<li>contact: <a href="mailto:globaleffect@gmail.com">globaleffect@gmail.com</a></li><ul>';
				echo $category_output;
				?>
			</ul>
		</div>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<?php
	$photo_db = PhotoQSingleton::getInstance('PhotoQDB');
	$photo = $photo_db->getPublishedPhoto($post->ID);
	
	if(isset($photo)){
		echo $photo->generateImgTag('main', '');
	}
?>

<?php endwhile; endif; ?>

		<div id="right-menu" class="menu">
			<ul>
				<?php echo $category_output; ?>
			</ul>
		</div>
	</div> <!-- #content -->
</div> <!-- #container -->

<?php get_footer() ?>