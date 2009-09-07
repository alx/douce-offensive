<?php
/*
Plugin Name: ThickBox
Plugin URI: http://www.christianschenk.org/projects/wordpress-thickbox-plugin/
Description: Embed ThickBox into your posts and pages.
Version: 1.4
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
*/

#
# WordPress ThickBox plugin
# Copyright (C) 2008-2009 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#

# Identifier for various actions of this script (e.g. JavaScript and CSS)
define('THICKBOX_ACTION', 'thickbox_action');
# ThickBox JavaScript file
define('THICKBOX_JS', dirname(__FILE__).'/thickbox/thickbox.js');
# ThickBox CSS file
define('THICKBOX_CSS', dirname(__FILE__).'/thickbox/thickbox.css');
# SmoothBox JavaScript file
define('SMOOTHBOX_JS', dirname(__FILE__).'/smoothbox/smoothbox.js');
# SmoothBox CSS file
define('SMOOTHBOX_CSS', dirname(__FILE__).'/smoothbox/smoothbox.css');
# Path to this plugin
define('THICKBOX_URL', '/wp-content/plugins/thickbox');

/**
 * Available variants. Add your own variants to the array as you see fit.
 */
function get_thickbox_available_variant() {
	return array('default', 'de');
}

# Identifier for the options
define('OPTION_THICKBOX_VARIANT', 'thickbox_variant');
define('OPTION_THICKBOX_EXPERT_MODE', 'thickbox_expert_mode');
define('OPTION_THICKBOX_INCLUDE_THICKBOX_JS', 'thickbox_include_thickbox_js');
define('OPTION_THICKBOX_INCLUDE_THICKBOX_CSS', 'thickbox_include_thickbox_css');
define('OPTION_THICKBOX_INCLUDE_JQUERY', 'thickbox_include_jquery');
define('OPTION_THICKBOX_USE_SMOOTHBOX', 'thickbox_use_smoothbox');
define('OPTION_THICKBOX_INCLUDE_MOOTOOLS', 'thickbox_include_mootools');


/**
 * Returns the contents for the given file and variant. For security reasons
 * the variant's name may only contain letters, numbers, a dash and an
 * underscore.
 *
 * Basically, the variant's filename should be of the following format:
 *   (thickbox|smoothbox)(.[a-zA-Z0-9_-]*)?.(js|css)
 * E.g.: thickbox.js, thickbox.my_variant.js, smoothbox.special.css
 */
function thickbox_get_variant_file_contents($filename, $variant) {
	$variant = ($variant == 'default') ? '' : preg_replace('/[^a-zA-Z0-9_-]/', '', $variant);
	$file = (empty($variant)) ? $filename : preg_replace('/(\.)(css|js)/', '.'.$variant.'.\2', $filename);
	if (is_file($file) == false) $file = $filename;
	return file_get_contents($file);
}


/**
 * Parses the actions
 */
if (!empty($_REQUEST[THICKBOX_ACTION])) {
	$variant = (isset($_REQUEST['variant'])) ? $_REQUEST['variant'] : 'default';

	switch ($_REQUEST[THICKBOX_ACTION]) {
		# ThickBox CSS
		case 'tcss':
			header('Content-type: text/css');
			$data = thickbox_get_variant_file_contents(THICKBOX_CSS, $variant);
			$data = str_replace('<URL>', $_REQUEST['url'], $data);
    		echo $data;
			break;
		# SmoothBox CSS
		case 'scss':
			header('Content-type: text/css');
			echo thickbox_get_variant_file_contents(SMOOTHBOX_CSS, $variant);
			break;
		# ThickBox JavaScript
		case 'tjs':
			header('Content-type: text/javascript');
			$data = thickbox_get_variant_file_contents(THICKBOX_JS, $variant);
			$data = str_replace('<URL>', $_REQUEST['url'], $data);
    		echo $data;
			break;
		# SmoothBox JavaScript
		case 'sjs':
			header('Content-type: text/javascript');
			echo thickbox_get_variant_file_contents(SMOOTHBOX_JS, $variant);
			break;
		default:
			break;
	}

	die();
}


/**
 * ThickBox init.
 */
function thickbox_init() {
	// i18n
	if (function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain('thickbox', 'wp-content/plugins/thickbox/messages');
	}

	/*
	 * Since we're using ThickBox on our options page we'll have to enqueue
	 * ThickBox in the admin interface.
	 * It's okay to rely on WordPress's mechanism to load ThickBox here.
	 */
	if (is_admin() and strpos($_SERVER['QUERY_STRING'], 'thickbox') !== false) {
    	wp_enqueue_script('thickbox');            
    	wp_enqueue_style('thickbox');            
	}
}    
if (function_exists('add_action'))
	add_action('init', thickbox_init);


/**
 * Checks whether ThickBox is enabled for the current post or page.
 */
function is_thickbox_enabled() {
	# if this flag is true we'll not check for a custom field
	if (thickbox_get_option(OPTION_THICKBOX_EXPERT_MODE) == false) return true;

	global $post;
	if (! isset($post)) return false;

	# check the custom field "thickbox"
	$meta = get_post_meta($post->ID, 'thickbox', true);
	if (empty($meta)) return false;

	return true;
}


/**
 * Adds a link to the CSS stylesheet in the header.
 */
function add_thickbox_css() {
	if (is_thickbox_enabled() === false) return;

	$url = get_bloginfo('wpurl').THICKBOX_URL;
	$variant = thickbox_get_option(OPTION_THICKBOX_VARIANT);
	$use_smoothbox = thickbox_get_option(OPTION_THICKBOX_USE_SMOOTHBOX);
	$include_thickbox_css = thickbox_get_option(OPTION_THICKBOX_INCLUDE_THICKBOX_CSS);

	if ($use_smoothbox == true) {
		$href = $url.'/thickbox.php?'.THICKBOX_ACTION.'=scss&amp;variant='.$variant;
	} else {
		$imgurl = get_bloginfo('wpurl').THICKBOX_URL.'/thickbox/images/';
		$href = $url.'/thickbox.php?'.THICKBOX_ACTION.'=tcss&amp;url='.urlencode($imgurl).'&amp;variant='.$variant;
	}

	if ($include_thickbox_css == true or $use_smoothbox == true)
		echo '<link rel="stylesheet" type="text/css" href="'.$href.'" />';
}
if (function_exists('add_action'))
	add_action('wp_head', 'add_thickbox_css');


/**
 * This will add the JavaScript to the footer.
 */
function add_thickbox_js() {
	if (is_thickbox_enabled() === false) return;

	$url = get_bloginfo('wpurl').THICKBOX_URL;
	$variant = thickbox_get_option(OPTION_THICKBOX_VARIANT);
	$include_thickbox_js = thickbox_get_option(OPTION_THICKBOX_INCLUDE_THICKBOX_JS);
	$include_jquery = thickbox_get_option(OPTION_THICKBOX_INCLUDE_JQUERY);
	$use_smoothbox = thickbox_get_option(OPTION_THICKBOX_USE_SMOOTHBOX);
	$include_mootools = thickbox_get_option(OPTION_THICKBOX_INCLUDE_MOOTOOLS);

	echo "\n";
	if ($use_smoothbox == true) {
		if ($include_mootools)
			echo '<script src="'.$url.'/smoothbox/mootools.js" type="text/javascript"></script>'."\n";
		echo '<script src="'.$url.'/thickbox.php?'.THICKBOX_ACTION.'=sjs&amp;variant='.$variant.'" type="text/javascript"></script>'."\n";
	} else {
		$imgurl = get_bloginfo('wpurl').THICKBOX_URL.'/thickbox/images/';
		if ($include_jquery == true)
			echo '<script src="'.$url.'/thickbox/jquery.js" type="text/javascript"></script>'."\n";
		if ($include_thickbox_js == true)
			echo '<script src="'.$url.'/thickbox.php?'.THICKBOX_ACTION.'=tjs&amp;url='.urlencode($imgurl).'&amp;variant='.$variant.'" type="text/javascript"></script>'."\n";
	}
}
if (function_exists('add_action'))
	add_action('wp_footer', 'add_thickbox_js');


/**
 * Adds a page ThickBox to the settings in the admin interface.
 */
function thickbox_add_options_page() {
	if(function_exists('add_options_page'))
		add_options_page('ThickBox', 'ThickBox', 5, basename(__FILE__), 'thickbox_show_options_page');
}
if (function_exists('add_action'))
	add_action('admin_menu', 'thickbox_add_options_page');


/**
 * Managers the options page.
 */
function thickbox_show_options_page() {
	$variant = thickbox_get_option(OPTION_THICKBOX_VARIANT);
	$expert_mode = thickbox_get_option(OPTION_THICKBOX_EXPERT_MODE);
	$include_thickbox_js = thickbox_get_option(OPTION_THICKBOX_INCLUDE_THICKBOX_JS);
	$include_thickbox_css = thickbox_get_option(OPTION_THICKBOX_INCLUDE_THICKBOX_CSS);
	$include_jquery = thickbox_get_option(OPTION_THICKBOX_INCLUDE_JQUERY);
	$use_smoothbox = thickbox_get_option(OPTION_THICKBOX_USE_SMOOTHBOX);
	$include_mootools = thickbox_get_option(OPTION_THICKBOX_INCLUDE_MOOTOOLS);

	if(isset($_POST['updateoptions'])) {
		$variant = $_POST['variant'];
		$expert_mode = $_POST['expert_mode'];
		$include_thickbox_js = $_POST['include_thickbox_js'];
		$include_thickbox_css = $_POST['include_thickbox_css'];
		$include_jquery = $_POST['include_jquery'];
		$use_smoothbox = $_POST['use_smoothbox'];
		$include_mootools = $_POST['include_mootools'];

		if (!isset($expert_mode)) $expert_mode = false; else $expert_mode = true;
		if (!isset($include_thickbox_js)) $include_thickbox_js = false; else $include_thickbox_js = true;
		if (!isset($include_thickbox_css)) $include_thickbox_css = false; else $include_thickbox_css = true;
		if (!isset($include_jquery)) $include_jquery = false; else $include_jquery = true;
		if (!isset($use_smoothbox)) $use_smoothbox = false; else $use_smoothbox = true;
		if (!isset($include_mootools)) $include_mootools = false; else $include_mootools = true;

		update_option(OPTION_THICKBOX_VARIANT, $variant);
		update_option(OPTION_THICKBOX_EXPERT_MODE, $expert_mode);
		update_option(OPTION_THICKBOX_INCLUDE_THICKBOX_JS, $include_thickbox_js);
		update_option(OPTION_THICKBOX_INCLUDE_THICKBOX_CSS, $include_thickbox_css);
		update_option(OPTION_THICKBOX_INCLUDE_JQUERY, $include_jquery);
		update_option(OPTION_THICKBOX_USE_SMOOTHBOX, $use_smoothbox);
		update_option(OPTION_THICKBOX_INCLUDE_MOOTOOLS, $include_mootools);

		echo '<div class="updated"><p><strong>'.__('Options saved.', 'thickbox').'</strong></p></div>';
	}
?>
<div class="wrap">
<h2>ThickBox</h2>
<form method="post" action="">
	<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Variant', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on variant', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_VARIANT; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td>
				<select name="variant">
					<?php foreach (get_thickbox_available_variant() as $available_variant) { ?>
      				<option <?php if ($available_variant == $variant) echo 'selected="selected" '; ?>value="<?php echo $available_variant; ?>"><?php echo ($available_variant == 'default' ? __('Default', 'thickbox') : $available_variant); ?></option>
					<?php } ?>
    			</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Expert mode', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on expert mode', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_EXPERT_MODE; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="expert_mode" value="1" <?php if ($expert_mode == true) echo 'checked="checked"'; ?>/></td>
		</tr>
	</table>
<h3><?php _e('Include JavaScript and CSS', 'thickbox'); ?></h3>
	<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Include ThickBox JavaScript', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on ThickBox JavaScript, CSS and jQuery', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_INCLUDE_THICKBOX_JS; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="include_thickbox_js" value="1" <?php if ($include_thickbox_js == true) echo 'checked="checked"'; ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Include ThickBox CSS', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on ThickBox JavaScript, CSS and jQuery', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_INCLUDE_THICKBOX_JS; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="include_thickbox_css" value="1" <?php if ($include_thickbox_css == true) echo 'checked="checked"'; ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Include jQuery', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on ThickBox JavaScript, CSS and jQuery', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_INCLUDE_THICKBOX_JS; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="include_jquery" value="1" <?php if ($include_jquery == true) echo 'checked="checked"'; ?>/></td>
		</tr>
	</table>
<h3>SmoothBox</h3>
	<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Use SmoothBox instead of ThickBox', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on SmoothBox', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_USE_SMOOTHBOX; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="use_smoothbox" value="1" <?php if ($use_smoothbox == true) echo 'checked="checked"'; ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Include Mootools', 'thickbox'); ?>:<br/>
			<small><a class="thickbox" title="<?php _e('Help on Mootools', 'thickbox'); ?>" href="#TB_inline?height=480&amp;width=600&amp;inlineId=<?php echo OPTION_THICKBOX_INCLUDE_MOOTOOLS; ?>"><?php _e('Help...', 'thickbox'); ?></a></small></th>
			<td><input type="checkbox" name="include_mootools" value="1" <?php if ($include_mootools == true) echo 'checked="checked"'; ?>/></td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="updateoptions" value="<?php _e('Update', 'thickbox'); ?>" />
	</p>
</form>

<h3><?php _e('Help and support', 'thickbox'); ?></h3>
<p><?php _e('Do you need help with this plugin? Do not hesitate to leave a <a href="http://www.christianschenk.org/projects/wordpress-thickbox-plugin/#respond" onclick="window.open(this.href,\'_blank\'); return false;">comment</a> on the plugin\'s page or <a href="http://www.christianschenk.org/contact/" onclick="window.open(this.href,\'_blank\'); return false;">contact</a> the author.', 'thickbox'); ?></p>
<p><?php _e('Do you like the plugin and want to <a href="http://www.christianschenk.org/donation/" onclick="window.open(this.href,\'_blank\'); return false;">support</a> it? It\'s much appreciated!', 'thickbox'); ?></p>
</div>

<div style="display: none;" id="<?php echo OPTION_THICKBOX_VARIANT; ?>">
<p><?php _e('This option helps you to choose between variants of the JavaScript and CSS that have been placed in the plugins directory under either <code>thickbox</code> or <code>smoothbox</code>. With this possibility it is easy to manage different styles with this single plugin.', 'thickbox'); ?></p>
<p><?php _e('If you would like to add your own variant, say, because you would like to translate the JavaScript to french or tweak the stylesheet it is a easy as this:', 'thickbox') ?>
<ul>
<li><?php _e('Place your modified files under <code>wp-content/plugins/thickbox</code> either into the directory <code>thickbox</code> or <code>smoothbox</code>, depending on the fact whether you are using ThickBox or SmoothBox.', 'thickbox'); ?></li>
<li><?php _e('Name the files according to this schema: <pre>   (thickbox|smoothbox)(.[a-zA-Z0-9_-]*)?.(js|css)</pre> If you can not read this RegEx, filenames like the following are okay: thickbox.js, thickbox.my_special_style.css, smoothbox.test.js.<br/>Simply add the name of your variant in front of the file extension (<code>js</code> or <code>css</code>) surrounded by two dots, consisting of letters, numbers, a dash or an underscore sign.', 'thickbox'); ?></li>
<li><?php _e('Add the name of your variant - e.g. <em>my_special_style</em> - to the array inside the function <code>get_thickbox_available_variant</code> right at the top of the file <code>thickbox.php</code>.', 'thickbox'); ?></li>
<li><?php _e('Finally you can enable you own variant via the options page.', 'thickbox'); ?></li>
</ul>
</p>
<p><?php _e('Note that if the plugin can not find the file for a given variant it will fall back to the default.', 'thickbox') ?>
</div>

<div style="display: none;" id="<?php echo OPTION_THICKBOX_EXPERT_MODE; ?>">
<p><?php _e('By default this plugin includes the JavaScript and CSS for ThickBox or SmoothBox on each and every page of your website. If you would like to save some bandwidth or perfrom better in Yahoo\'s <a href="http://developer.yahoo.com/yslow/" onclick="window.open(this.href,\'_blank\'); return false;">YSlow</a> or Google\'s <a href="http://code.google.com/speed/page-speed/" onclick="window.open(this.href,\'_blank\'); return false;">Page Speed</a> you may want to advise the plugin to load the JavaScript/CSS only on those pages that you are using a ThickBox/SmoothBox on.', 'thickbox'); ?></p>
<p><?php _e('Once you have enabled the <em>expert mode</em> the plugin won\'t include the JavaScript or CSS by default. You will have to add a custom field called <code>thickbox</code> to the page where you would like to use ThickBox/SmoothBox; set the value to anything you like, e.g. "On".', 'thickbox'); ?></p>
</div>

<div style="display: none;" id="<?php echo OPTION_THICKBOX_INCLUDE_THICKBOX_JS; ?>">
<p><?php _e('With this setting you are able to fine tune the inclusion of ThickBox\'s JavaScript, CSS and jQuery which is used by ThickBox. Say, jQuery is already included in your theme\'s header you may want to disable it here; this way it won\'t show up twice in your site\'s content.', 'thickbox'); ?></p>
<p><?php _e('Note that if you deactivate the JavaScript as well as the CSS for ThickBox this plugin is quite pointless. In this case you should think about the reason why you wanted to use the plugin in the first place.', 'thickbox'); ?></p>
</div>

<div style="display: none;" id="<?php echo OPTION_THICKBOX_USE_SMOOTHBOX; ?>">
<p><?php _e('If you have to deliver valid HTML or CSS SmoothBox might be a nice replacement for ThickBox. Once you have activated this option the plugin will use SmoothBox instead of ThickBox.', 'thickbox'); ?></p>
</div>

<div style="display: none;" id="<?php echo OPTION_THICKBOX_INCLUDE_MOOTOOLS; ?>">
<p><?php _e('If you are using SmoothBox and your theme already includes Mootools you can deactivate it here; this way it won\'t show up twice in your site\'s content.', 'thickbox'); ?></p>
</div>
<?php
}


/**
 * Corrects the style for content inside our ThickBox.
 */
function add_thickbox_admin_css() {
	if (!is_admin() and strpos($_SERVER['QUERY_STRING'], 'thickbox') === false ) return;
?>
<style type="text/css">
#TB_ajaxContent ul, #TB_ajaxContent ol {
	margin-left: 2em;
	list-style-type: square;
}
</style>
<?php }
if (function_exists('add_action'))
	add_action('admin_head', 'add_thickbox_admin_css');


/**
 * Deletes all options from the database.
 */
function thickbox_deactivate() {
	delete_option(OPTION_THICKBOX_VARIANT);
	delete_option(OPTION_THICKBOX_EXPERT_MODE);
	delete_option(OPTION_THICKBOX_INCLUDE_THICKBOX_JS);
	delete_option(OPTION_THICKBOX_INCLUDE_THICKBOX_CSS);
	delete_option(OPTION_THICKBOX_INCLUDE_JQUERY);
	delete_option(OPTION_THICKBOX_USE_SMOOTHBOX);
	delete_option(OPTION_THICKBOX_INCLUDE_MOOTOOLS);
}
if (function_exists('add_action'))
	add_action('deactivate_thickbox/thickbox.php', 'thickbox_deactivate');


/**
 * Returns the option for the given identifier. If it's not in the database
 * we'll fall back to default values.
 */
function thickbox_get_option($identifier) {
	$option = get_option($identifier);
	if ($option !== false)
		return ($identifier == OPTION_THICKBOX_VARIANT) ? $option : (bool) $option;

	# return defaults
	switch ($identifier) {
		case OPTION_THICKBOX_VARIANT: return 'default';
		case OPTION_THICKBOX_EXPERT_MODE: return false;
		case OPTION_THICKBOX_INCLUDE_THICKBOX_JS: return true;
		case OPTION_THICKBOX_INCLUDE_THICKBOX_CSS: return true;
		case OPTION_THICKBOX_INCLUDE_JQUERY: return true;
		case OPTION_THICKBOX_USE_SMOOTHBOX: return false;
		case OPTION_THICKBOX_INCLUDE_MOOTOOLS: return true;
		default: return null;
	}
}

?>
