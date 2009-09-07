<?php
/**
 * @package WordPress
 * @subpackage Classic_Theme
 */

if(!defined(PHOTOQ_PATH)){
	//convert backslashes (windows) to slashes
	$cleanPath = str_replace('\\', '/', dirname(__FILE__ . '/') . "/../../plugins/photoq-photoblog-plugin/");
	define('PHOTOQ_PATH', $cleanPath);
}

//include all classes and libraries needed by PhotoQ
if (!class_exists("PhotoQ")) {
	//Load PEAR_ErrorStack which is used for error handling.
	
	//careful if some other plugin already required ErrorStack (but from
	//a different path we are not allowed to redefine
	if (!class_exists("ErrorStack"))
		require_once(PHOTOQ_PATH.'lib/PEAR_ErrorStack/ErrorStack.php');
	
	//include the other files required by photoq
	require_once(PHOTOQ_PATH.'classes/PhotoQObject.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQError.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQHelpers.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQ.php');
	
	// import ReusableOptions Library, same here add safety check
	if (!class_exists("OptionController"))
		require_once(PHOTOQ_PATH.'lib/ReusableOptions/OptionController.php');
	//import remaining PhotoQ classes
	require_once(PHOTOQ_PATH.'classes/PhotoQOptionController.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQDB.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQQueue.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQBatch.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQPhoto.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQExif.php');
	require_once(PHOTOQ_PATH.'classes/PhotoQImageSize.php');
}

$photoq = new PhotoQ();

automatic_feed_links();

if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '',
		'after_title' => '',
	));

function display_recent_categories(){
	global $wpdb, $wp_rewrite;
	
	$query = "select t.term_id as term_ids, t.name, t.slug, max(p.ID) as id, tx.count from $wpdb->terms t, $wpdb->term_taxonomy tx, $wpdb->term_relationships tr, $wpdb->posts p where t.term_id = tx.term_id and tx.parent != 0 and tx.taxonomy = 'category' and tr.term_taxonomy_id = t.term_id and tr.object_id = id and p.post_status = 'publish' group by term_ids order by id desc limit 5";
	
	$results = $wpdb->get_results($query);
	
	foreach ($results as $result) {
		$catlink = str_replace( '%category%', $result->slug, $wp_rewrite->get_category_permastruct());
		$catlink = get_option( 'home' ) . user_trailingslashit( $catlink, 'category' );
		echo "<li><a href='".$catlink."'>".$result->name."</a></li>";
	}
}

function display_selected_categories(){
	//display_categories_from_posts('category_name=albums&orderby=date&order=DESC&tag=selected');
	
	global $wpdb;
	
	$query = "select t.term_id as term_ids, t.name, t.slug, max(p.ID) as id, tx.count from $wpdb->terms t, $wpdb->term_taxonomy tx, $wpdb->term_relationships tr, $wpdb->posts p where t.name = 'selected' and t.term_id = tx.term_id and tx.parent != 0 and tx.taxonomy = 'category' and tr.term_taxonomy_id = t.term_id and tr.object_id = id and p.post_status = 'publish' group by term_ids order by id desc limit 5";
	
	echo $query;
	
	$results = $wpdb->get_results($query);
	
	foreach ($results as $result) {
		echo "<li><a href='".get_category_link($result->id)."'>".$result->name."</a></li>";
	}
}

?>
