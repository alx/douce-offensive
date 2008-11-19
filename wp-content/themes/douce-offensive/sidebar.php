	<div id="sidebar">
		<ul>
			<?php if ( ! function_exists('dynamic_sidebar') || ! dynamic_sidebar()) : // only show our sidebar here if dynamic_sidebar() can't build widgets ?>

				<?php wp_list_categories('show_count=1&title_li=<h2>' . __('', 'skimmed') . '</h2>'); ?>
			<?php endif; // end if can't use widgets ?>
		</ul>
	</div><!-- sidebar -->
