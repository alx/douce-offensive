<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />

	<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'skimmed'), bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<?php wp_head(); ?>
</head>

<body>
		
<div id="wrapper" style="width: 2600px;">
	<div style="width: 2100px; top: 0px;" id="layout">
		<div id="outside" style="position: fixed; right: 102px;">
				<div id="outside-container">
					
					<ul id="searchlink">
						<li><a href="/fr/search/">rechercher</a></li>
					</ul>
					
					<ul class="metalink">
						<li><a href="#">Contact</a></li>
						<li><a href="#">Admin</a></li>
					</ul>
				</div>

				<div style="position: absolute; height: 504px; width: 236px; top: 60px; left: 0pt;">
					<div class="menu" id="menu-left" style="top: 0pt;">
						<div class="background" id="menu-left-background" style="left: -472px;"/>
						<div class="scroll" id="menu-left-scroll" style="left: -472px; top: 0px;">
							<div class="pagemenu" id="menu-left-pagemenu">
								<div class="title"><?php wp_title(); ?></div>

								<ul class="subselection">		
									<li><a class="active" href="#">Categorie</a></li>
									<?php display_categories(); ?>
								</ul>

								<p class="level-up"><a href="#">Autres catégories</a></p>
							</div>
						</div>
					</div>
					<div class="shadow-bottom">
						<div class="start"/>
					</div>
				</div>

				<div style="height: 504px; margin-left: 236px;" id="content">