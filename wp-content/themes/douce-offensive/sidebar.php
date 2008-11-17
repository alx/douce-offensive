<!-- RIGHT SIDEBAR -->

<ul>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>

<!-- SEARCH THE SITE -->
<form method="get" action="<?php bloginfo('url'); ?>/">
<li><input id="searchbar" value="search" type="text" name="s" id="s" onclick="clickclear(this, 'search')" onblur="clickrecall(this,'search')"  /><input id="searchbutton" type="submit" value="&raquo;" />
</form>

<!-- DROPDOWN MENU -->
<li><select id="dropdown" name=\"archive-dropdown\" onChange='document.location.href=this.options[this.selectedIndex].value;'>
<option value=\"\"><?php echo attribute_escape(__('archives')); ?></option>
<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>

<!-- TAG CLOUD -->  
 <li id="categories"><?php _e('tag cloud'); ?>
	<ul>
		<?php wp_tag_cloud('smallest=6&largest=12'); ?>
	</ul>
</li>
   
<!-- YOUR LINKS -->
<?php get_links_list(); ?>

<?php endif; ?>

</ul>
