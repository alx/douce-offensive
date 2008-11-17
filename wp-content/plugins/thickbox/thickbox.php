<?php
/*
Plugin Name: ThickBox
Plugin URI: http://www.christianschenk.org/projects/wordpress-thickbox-plugin/
Description: Embed ThickBox into your posts and pages.
Version: 1.2.1
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
*/

#
# WordPress ThickBox plugin
# Copyright (C) 2008 Christian Schenk
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

# Identifier for various actions of this script (e.g. css)
define('THICKBOX_ACTION', 'thickbox_action');
# The JS file
define('THICKBOX_JS', dirname(__FILE__).'/thickbox/thickbox.js');
# The CSS file
define('THICKBOX_CSS', dirname(__FILE__).'/thickbox/thickbox.css');
# Path to this plugin
define('THICKBOX_URL', '/wp-content/plugins/thickbox');
# Include JavaScript and CSS in every page so you don't have to add a custom
# field "thickbox" to your posts - i.e. more fun, less hassle
define('INCLUDE_JS_AND_CSS_EVERYWHERE', true);
# If you set the following to false the CSS/JavaScript won't be inserted.
# Useful if your theme does this for you - if you don't include both, this
# plugin is pointless.
define('INCLUDE_THICKBOX_CSS', true);
define('INCLUDE_THICKBOX_JAVASCRIPT', true);
# Set this to false if you've already got jQuery
define('INCLUDE_JQUERY', true);
# Include Smoothbox instead of original ThickBox
define('INCLUDE_SMOOTHBOX', false);


/**
 * Parses the actions
 */
if (!empty($_REQUEST[THICKBOX_ACTION])) {
	switch ($_REQUEST[THICKBOX_ACTION]) {
		case 'css':
			$contenttype = 'css';
			$cssOrJsFile = THICKBOX_CSS;
			break;
		case 'js':
			$contenttype = 'javascript';
			$cssOrJsFile = THICKBOX_JS;
			break;
		default:
			die();
			break;
	}

	header('Content-type: text/'.$contenttype);

	$data = file_get_contents($cssOrJsFile);
	$data = str_replace('<URL>', $_REQUEST['url'], $data);
	echo $data;

	die();
}


/**
 * Checks whether ThickBox is enabled for the current post or page.
 */
function is_thickbox_enabled() {
	# if this flag is true we'll not check for a custom field
	if (INCLUDE_JS_AND_CSS_EVERYWHERE === true) return true;

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

	if (INCLUDE_SMOOTHBOX) {
		$href = $url.'/smoothbox/smoothbox.css';
	} else {
		$imgurl = get_bloginfo('wpurl').THICKBOX_URL.'/thickbox/images/';
		$href = $url.'/thickbox.php?'.THICKBOX_ACTION.'=css&amp;url='.urlencode($imgurl);
	}

	if (INCLUDE_THICKBOX_CSS or INCLUDE_SMOOTHBOX) echo '<link rel="stylesheet" type="text/css" href="'.$href.'" />';
}
if (function_exists('add_action')) add_action('wp_head', 'add_thickbox_css');


/**
 * This will add the JavaScript to the footer.
 */
function add_thickbox_js() {
	if (is_thickbox_enabled() === false) return;

	$url = get_bloginfo('wpurl').THICKBOX_URL;

	echo "\n";
	if (INCLUDE_SMOOTHBOX) {
		echo '<script src="'.$url.'/smoothbox/mootools.v1.11.js" type="text/javascript"></script>'."\n";
		echo '<script src="'.$url.'/smoothbox/smoothbox.js" type="text/javascript"></script>'."\n";
	} else {
		$imgurl = get_bloginfo('wpurl').THICKBOX_URL.'/thickbox/images/';
		if (INCLUDE_JQUERY) echo '<script src="'.$url.'/thickbox/jquery.js" type="text/javascript"></script>'."\n";
		if (INCLUDE_THICKBOX_JAVASCRIPT) echo '<script src="'.$url.'/thickbox.php?'.THICKBOX_ACTION.'=js&amp;url='.urlencode($imgurl).'" type="text/javascript"></script>'."\n";
	}
}
if (function_exists('add_action')) add_action('wp_footer', 'add_thickbox_js');

?>
