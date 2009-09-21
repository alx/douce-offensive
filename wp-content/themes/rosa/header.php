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
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
	
	
	<script type="text/javascript" src="<?php bloginfo('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/rosa.js"></script>
</head>

<body <?php body_class(); ?>>
	
	<div id="header">
		
		<div id="logo">
			<a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>
		</div>
		
		<div class="nav">
			<h2>Archivo &mdash;</h2>
			<ul>
			<?php 
				display_recent_categories();
			?>
			</ul>
		</div>
		
		<div class="nav talleres-nav">
			<h2>Talleres de labor creativa &mdash;</h2>
			<ul>
				<li><a href="/visitas-a-exposiciones/">visitas a exposiciones</a></li>
				<li><a href="/fotos-de-los-alumnos/">fotos de los alumnos</a></li>
				<li><a href="/antisafari-fotografico/">antisafari fotogr&aacute;fico</a></li>
			</ul>
		</div>
		
		<div class="nav">
			<h2>Direcciones &mdash;</h2>
			<ul>
				<!-- <li><a href="#">links</a></li>
								<li><a href="#">blog</a></li> -->
				<li><a href="mailto:rosaveloso.foto@gmail.com">contacto</a></li>
			</ul>
		</div>
	</div>

	<div class="clear_float"></div>

	<div id="content">
	<!-- end header -->
