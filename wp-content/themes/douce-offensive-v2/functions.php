<?php

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

function display_image() {
	global $post;
	
	$photo = PhotoQSingleton::getInstance('PhotoQDB')->getPublishedPhoto($post->ID);
	echo "<img src='" . $photo->_sizes["main"]->getUrl() . "' class='full' id='showroom_image_" . $post->ID . "'/>";
}

function display_categories() {
	$categories = get_categories();

	$output = '';
	foreach($categories as $cat):
		$output .= "<p><a name='" . attribute_escape($cat->name) . "' class='menuheader' href='#'>";
		$output .= attribute_escape($cat->name) . "'</a></p>";
	endforeach;
	
	echo $output;
}

?>