	<div id="footer">
		<p><small>
		<?php
			printf(__('%1$s uses %2$s with %3$s and feeds you %4$s and %5$s', 'skimmed'),
				'<a href="' . get_option('home') . '" title="' . sprintf(__('%s home page', 'skimmed'), get_bloginfo('name', 'display')) . '">' . get_bloginfo('name', 'display') . '</a>',
				'<a href="http://wordpress.org" title="' . __('Wordpress open source blogging software', 'skimmed') . '">WordPress</a>',
				'<a href="http://thortz.com/skimmed-milk/" title="' . __('A Wordpress theme by Thortz based on White As Milk by Azeem Azeez', 'skimmed') . '">Skimmed Milk</a>',
				'<a href="' . get_bloginfo('rss2_url', 'display') . '" title="' . __('RSS feed for recent posts', 'skimmed') . '">' . __('Posts', 'skimmed') . '</a>',
				'<a href="' . get_bloginfo('comments_rss2_url', 'display') . '" title="' . __('RSS feed for comments on recent posts', 'skimmed') . '">' . __('Comments', 'skimmed') . '</a>');
		?>

		<?php global $user_ID, $user_identity; get_currentuserinfo(); if ($user_ID) : // only show the following if are logged in ?>
			<br />
			<span class="ui">
			<?php
				printf(__('Valid %1$s and %2$s. ', 'skimmed'),
					'<a href="http://validator.w3.org/check/referer" title="' . __('Validate the XHTML source for this page', 'skimmed') . '">XHTML</a>',
					'<a href="http://jigsaw.w3.org/css-validator/check/referer" title="' . __('Validate the CSS stylesheet for this page', 'skimmed') . '">CSS</a>');
				printf(__('%1$u queries in %2$ss. ', 'skimmed'), $wpdb->num_queries, timer_stop(0));
				printf(__('Logged in as %s', 'skimmed'),
					'<a href="' . get_option('siteurl') . '/wp-admin/profile.php" title="' . __('Link to your profile', 'skimmed') . '">' . $user_identity . '</a>');
			?>
			<strong>|</strong> <span class="nowrap"><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout&redirect_to=<?php echo $_SERVER["REQUEST_URI"]; ?>" 
				title="<?php _e('Log out of this account', 'skimmed'); ?>"><?php _e('Log out', 'skimmed'); ?> &raquo;</a></span>
			</span><!-- ui -->
		<?php endif; // end if logged in ?>

		</small></p>

	</div><!-- footer -->

	<div class="snap-to-fit"></div>

</div><!-- page -->

<?php do_action('wp_footer'); ?>

</body>
</html>
