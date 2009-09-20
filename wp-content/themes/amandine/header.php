<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/print.css" type="text/css" media="print" />

	<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'skimmed'), bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<?php wp_head(); ?>
	<script type="text/javascript" src="<?php bloginfo('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/douce-offensive.js"></script>
</head>

<body>
<div id="page"><?php // Note html, body & page are all closed by footer.php ?>

	<div id="header">
		<ul>
		<?php
		$categories = get_categories();
		$current_cat = get_query_var('cat');

		$output = '<ul>';
		$i = 1;
		foreach($categories as $cat):		
			$output .= '<li class="cat-item cat-item-'.$cat->term_id.'">';
			$output .= '<a title="' . sprintf(__( 'View all posts filed under %s' ), attribute_escape($cat->name)) . '"';
			
			if ($cat->ID == $current_cat->ID) {
				$output .= ' href="' . get_category_link( $cat->term_id ) . '"><strong>' . attribute_escape($cat->name) . '</strong></a>';
			} else {
				$output .= ' href="' . get_category_link( $cat->term_id ) . '">' . attribute_escape($cat->name) . '</a>';
			}
			
			
			$output .= ' (' . intval($cat->count) . ')</li>';
			
			if($i%3 == 0) $output .= '</ul><ul>';
			$i += 1;
		endforeach;
		
		$output .= '<li><a href="mailto:amandin.le.conte@free.fr">contact</a></li><ul>';
		echo $output;
		?>
		</ul>
	</div><!-- header -->
