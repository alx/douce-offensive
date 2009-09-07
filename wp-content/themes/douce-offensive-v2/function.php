<?php

if (!class_exists('IQ2Theme')) {
	//convert backslashes (windows) to slashes
	$cleanPath = str_replace('\\', '/', dirname(__FILE__));
	define('IQ2_PATH', $cleanPath.'/');
	require_once(IQ2_PATH.'classes/IQ2Theme.php');
}

if (class_exists('IQ2Theme')) {
	$iQ2Theme = new IQ2Theme();
}

?>