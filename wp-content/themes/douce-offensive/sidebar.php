	<div id="sidebar">
		<ul>
			<?php if ( ! function_exists('dynamic_sidebar') || ! dynamic_sidebar()) : // only show our sidebar here if dynamic_sidebar() can't build widgets ?>

				<?php wp_list_pages('title_li=<h2>' . __('Pages', 'skimmed') . '</h2>' ); ?>

				<?php wp_list_categories('show_count=1&title_li=<h2>' . __('Topics', 'skimmed') . '</h2>'); ?>

				<li>
					<?php skimmed_milk_archive(); ?>
				</li>

				<?php wp_list_bookmarks(); ?>

				<li>
					<?php skimmed_milk_search(); ?>
				</li>
			<?php endif; // end if can't use widgets ?>
		</ul>
	</div><!-- sidebar -->
