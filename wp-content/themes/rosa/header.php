<?php
/**
 * @package WordPress
 * @subpackage Classic_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
	</style>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php wp_head(); ?>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/jquery.tools.min.js"></script>
</head>

<body <?php body_class(); ?>>
	
	<div id="header">
		
		<div id="logo">
			<a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>
		</div>
		
		<div class="nav">
			<h2>Recent &mdash;</h2>
			<ul>
			<?php 
				display_recent_categories();
			?>
			</ul>
		</div>
		
		<!--
		<div class="nav">
			<h2>Selected &mdash;</h2>
			<ul>
			<?php 
				display_selected_categories();
			?>
			</ul>
		</div>
		-->
		
		<div class="nav">
			<h2>Info &mdash;</h2>
			<ul>
			<?php 
				// Display list of pages
				wp_list_pages('title_li=&exclude_tree=5&sort_column=menu_order');
			?>
			</ul>
		</div>
	</div>

	<div class="clear_float"></div>

	<div id="content">
	<!-- end header -->
