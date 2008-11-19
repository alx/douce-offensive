<?php

// allows localisation (translation) of messages
load_theme_textdomain('skimmed');

// allows dynamic widgetised siebar to be used
if (function_exists('register_sidebar'))
	register_sidebar();




// styled search box
function skimmed_milk_search() {
?>
	<form method="get" name="searchform" id="searchform" action="<?php bloginfo('url'); ?>">
		<input type="text" value="<?php the_search_query(); ?>" name="s" class="s" tabindex="1"/><br />
		<script type="text/javascript">
		<!--
			document.write("<p id=\"submitsearch\"><a href=\"javascript:document.searchform.submit();\" title=\"<?php _e('Search all posts', 'skimmed'); ?>\"><?php _e('Search', 'skimmed'); ?></a></p>");
		//-->
		</script>
		<noscript>
			<input type="submit" name="submitsearch" id="submitsearch" tabindex="2" value="<?php _e('Search', 'skimmed'); ?>"/>
		</noscript>
	</form>
<?php
}


// the search box wrapped as a widget
function skimmed_milk_search_widget($args) {
	extract($args);
	echo $before_widget;
	skimmed_milk_search();
	echo $after_widget;
}
if (function_exists('register_sidebar_widget'))
	register_sidebar_widget("Skimmed search", 'skimmed_milk_search_widget');




// archive list with optional limit to number of months shown and optional display of yearly archives
function skimmed_milk_archive($limit = '', $years = false) {
?>
	<h2><?php _e('Archives', 'skimmed'); ?></h2>
	<ul>
		<?php
			wp_get_archives("type=monthly&limit=$limit");
			if ($years)
				wp_get_archives('type=yearly');
		?>
	</ul>
<?php
}


// the archive list as a widget with various controls
function skimmed_milk_archive_widget($args) {
	extract($args);
	$options = get_option('skimmed_milk_widget_archives');
	$title = empty($options['title']) ? __('Archives', 'skimmed') : $options['title'];
	$years = isset($options['years']) ? ($options['years'] ? '1' : '0') : '1';
	$counts = isset($options['counts']) ? ($options['counts'] ? '1' : '0') : '0';
	$months = skimmed_milk_ensure_numeric_or_empty($options['months']);
	
	echo $before_widget; 
	echo $before_title . $title . $after_title;
?>
	<ul>
	
	<?php
		wp_get_archives("type=monthly&limit=$months&show_post_count=$counts");
		if ($years)
			wp_get_archives("type=yearly&show_post_count=$counts");
	?>
	</ul>
<?php
	echo $after_widget;
}
if (function_exists('register_sidebar_widget'))
	register_sidebar_widget('Skimmed archives', 'skimmed_milk_archive_widget');


// a configuration panel for the archive list widget
function skimmed_milk_archive_widget_control() {
	$options = $newoptions = get_option('skimmed_milk_widget_archives');
	if ( $_POST['skimmed_archives-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['skimmed_archives-title']));
		$newoptions['months'] = skimmed_milk_ensure_numeric_or_empty($_POST['skimmed_archives-months']);
		$newoptions['years'] = isset($_POST['skimmed_archives-years']);
		$newoptions['counts'] = isset($_POST['skimmed_archives-counts']);
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('skimmed_milk_widget_archives', $options);
	}
	$title = attribute_escape($options['title']);
	$months = skimmed_milk_ensure_numeric_or_empty($options['months']);
	$years = $options['years'] ? 'checked="checked"' : '';
	$counts = $options['counts'] ? 'checked="checked"' : '';
?>
	<p style="text-align:left">
		<label for="skimmed_archives-title"><?php _e('Title:', 'skimmed'); ?>
			<input style="width: 250px;" id="skimmed_archives-title" name="skimmed_archives-title" type="text" value="<?php echo $title; ?>" />
		</label>
	</p>
	<p style="text-align:left">
		<label for="skimmed_archives-months"><?php _e('Maximum number of months:', 'skimmed'); ?>
			<input style="width: 25px;" id="skimmed_archives-months" name="skimmed_archives-months" type="text" value="<?php echo $months; ?>" />
		</label>
	</p>
	<p style="text-align:right;margin-right:90px;">
		<label for="skimmed_archives-years">
			<?php _e('Show yearly archives', 'skimmed'); ?>
			<input class="checkbox" type="checkbox" <?php echo $years; ?> id="skimmed_archives-years" name="skimmed_archives-years" />
		</label>
	</p>
	<p style="text-align:right;margin-right:90px;">
		<label for="skimmed_archives-counts">
			<?php _e('Show post counts', 'skimmed'); ?>
			<input class="checkbox" type="checkbox" <?php echo $counts; ?> id="skimmed_archives-counts" name="skimmed_archives-counts" />
		</label>
	</p>
	<input type="hidden" id="skimmed_archives-submit" name="skimmed_archives-submit" value="1" />
<?php
}
if (function_exists('register_widget_control'))
	register_widget_control('Skimmed archives', 'skimmed_milk_archive_widget_control');


// utility to ensure (non-negative) number or empty string
function skimmed_milk_ensure_numeric_or_empty($m) {
	if ((is_null($m) || rtrim($m) == "") && $m !== false)
		return '';
	else
		return max((int) $m, 0);
}




// simplified wp_widget_calendar without title
function skimmed_milk_calendar_widget($args) {
	extract($args);
	echo $before_widget;
	get_calendar();
	echo $after_widget;
}
if (function_exists('register_sidebar_widget'))
	register_sidebar_widget('Skimmed calendar', 'skimmed_milk_calendar_widget');



// as wp_widget_tag_cloud but with more options
function skimmed_milk_tag_cloud_widget($args) {
	extract($args);
	$options = get_option('skimmed_milk_widget_tag_cloud');
	$title = empty($options['title']) ? __('Tags', 'skimmed') : $options['title'];
	$smallest = skimmed_milk_check_number($options['smallest'], 1.0);
	$largest = skimmed_milk_check_number($options['largest'], 1.5);

	echo $before_widget;
	echo $before_title . $title . $after_title;
	wp_tag_cloud(array('smallest' => $smallest, 'largest' => $largest, 'unit' => 'em'));
	echo $after_widget;
}
if (function_exists('register_sidebar_widget'))
	register_sidebar_widget('Skimmed tag cloud', 'skimmed_milk_tag_cloud_widget');


// as wp_widget_tag_cloud_control but with more options
function skimmed_milk_tag_cloud_widget_control() {
	$options = $newoptions = get_option('skimmed_milk_widget_tag_cloud');

	if ( $_POST['skimmed-tag-cloud-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['skimmed-tag-cloud-title']));
		$newoptions['smallest'] = skimmed_milk_check_number($_POST['skimmed-tag-cloud-smallest'], 1.0);
		$newoptions['largest'] = skimmed_milk_check_number($_POST['skimmed-tag-cloud-largest'], 1.5);
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('skimmed_milk_widget_tag_cloud', $options);
	}

	$title = attribute_escape($options['title']);
	if (empty($title))
		$title = __('Tags', 'skimmed');
	$smallest = skimmed_milk_check_number($options['smallest'], 1.0);
	$largest = skimmed_milk_check_number($options['largest'], 1.5);
?>
	<p style="text-align:left">
		<label for="skimmed-tag-cloud-title"><?php _e('Title:', 'skimmed'); ?>
			<input style="width: 250px;" id="skimmed-tag-cloud-title" name="skimmed-tag-cloud-title" type="text" value="<?php echo $title; ?>" />
		</label>
	</p>
	<p style="text-align:left">
		<label for="skimmed-tag-cloud-smallest"><?php _e('Smallest:', 'skimmed'); ?>
			<input style="width: 25px;" id="skimmed-tag-cloud-smallest" name="skimmed-tag-cloud-smallest" type="text" value="<?php echo $smallest; ?>" />
		</label>
		ems
	</p>
	<p style="text-align:left">
		<label for="skimmed-tag-cloud-largest"><?php _e('Largest:', 'skimmed'); ?>
			<input style="width: 25px;" id="skimmed-tag-cloud-largest" name="skimmed-tag-cloud-largest" type="text" value="<?php echo $largest; ?>" />
		</label>
		ems
	</p>
	<input type="hidden" name="skimmed-tag-cloud-submit" id="skimmed-tag-cloud-submit" value="1" />
<?php
}
if (function_exists('register_widget_control'))
	register_widget_control('Skimmed tag cloud', 'skimmed_milk_tag_cloud_widget_control');


// utility to ensure positive number or default otherwise
function skimmed_milk_check_number($m, $default) {
	return $m > 0 ? $m : $default;
}





// navigation links in index, archive & search
function skimmed_milk_nav_link($left, $right) {
?>
	<div class="navigation">
	<?php if($left) : ?>
		<div class="alignleft"><?php previous_posts_link('&laquo; ' . $left); ?></div>
	<?php endif; if($right) : ?>
		<div class="alignright"><?php next_posts_link($right . ' &raquo;'); ?></div>
	<?php endif; ?>
		<div class="snap-to-fit"></div>
	</div><!-- navigation -->
<?php
}




// post titles in single, index, archive & search
function skimmed_milk_post_title($tag = 'h3') {
	echo $tag.' id="post-'.the_ID().'"'.the_title();
}




// info after post entries in index, archive & search
function skimmed_milk_post_meta() {
?>
	<p class="postmetadata"><?php printf(__('Posted in %s', 'skimmed'), get_the_category_list(', ', ''));
		the_tags(' <strong>|</strong> ' . __('Tagged ', 'skimmed'), ', ', ' '); ?>
		<strong>|</strong>
		<?php edit_post_link(__('Edit', 'skimmed'),'',' <strong>|</strong>'); ?>
		<span class="nowrap"><?php comments_popup_link(__('No responses', 'skimmed') . ' &raquo;', __('One response', 'skimmed') . ' &raquo;', '% ' . __('responses', 'skimmed') . ' &raquo;', '', __('Comments off', 'skimmed')); ?></span></p> 
<?php
}




// post content in single and page
function skimmed_milk_post_entry() {
	global $page, $numpages, $multipage;
	if ($multipage) {
		if ($page > $numpages)
			$page = $numpages;
		echo '<p><strong>' . sprintf(__('Page %1$s of %2$s', 'skimmed'), $page, $numpages) . '</strong></p>';
	}
	else
		$page = 1;

	the_content();

	wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'skimmed') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number'));
}




// post metadata dependent on comment & ping status in single and page
function skimmed_milk_post_comment_or_ping($comments_open, $trackbacks_allowed, $show_bar = true) {
	echo '<span class="ui">';
	if ($comments_open && $trackbacks_allowed) // both open
		printf(__('%1$sFeed%2$s on comments or %3$strackback%4$s from your site', 'skimmed'),
			'<a href="' . comments_rss() . '" title="' . __('RSS feed for comments on this post', 'skimmed') . '">',
			'</a>',
			'<a href="' . get_trackback_url() . '" rel="trackback" title="' . __('Copy this link to trackback this post from your own blog', 'skimmed') . '">',
			'</a>');
	elseif ($comments_open) // comments only
		printf(__('%sFeed%s on comments but note that pings and trackbacks are closed', 'skimmed'),
			'<a href="' . comments_rss() . '" title="' . __('RSS feed for comments on this post', 'skimmed') . '">',
			'</a>');
	elseif ($trackbacks_allowed) // pings only
		printf(__('Comments are closed but you may %strackback%s from your site', 'skimmed'),
			'<a href="' . get_trackback_url() . '" rel="trackback" title="' . __('Copy this link to trackback this post from your own blog', 'skimmed') . '">',
			'</a>');
	
	edit_post_link(__('Edit', 'skimmed'), $show_bar ? ' <strong>|</strong> ' : '', '');
	echo '</span>'; // class="ui";
}




// message and sympathy when page information not found
function skimmed_milk_something_not_found($what_not) {
?>
	<h2 class="pagetitle"><span class="problem"><?php echo $what_not; ?></span></h2>

	<p class="entry"><?php _e('Human or computer? One of us has slipped up.', 'skimmed'); ?></p>

	<p class="entry"><?php _e('To find pages that <strong>do</strong> exist, try using the links or the search box on the left or click the blog title to go to its home page.', 'skimmed'); ?></p>

	<p class="entry"><?php _e("But perhaps that's what you were doing anyway, when it all went wrong..?", 'skimmed'); ?></p>
<?php
}
?>
