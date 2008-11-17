<!-- RIGHT SIDEBAR -->

<div id="nav">

<ul>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>

<!-- LIST THE PAGES -->
 
<li id="categories"><?php _e('pages'); ?>

	<ul>

	<?php wp_list_pages('title_li= '); ?>

	</ul>

</li>

<!-- ADMIN CONSOLE -->

<li id="meta"><?php _e('admin'); ?>

 	<ul>

		<?php wp_register(); ?>

		<li><?php wp_loginout(); ?></li>

	</ul>

</li>

<?php endif; ?>

</ul>

</div><!-- end NAV -->