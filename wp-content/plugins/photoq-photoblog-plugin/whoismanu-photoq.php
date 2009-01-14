<?php
/*
 Plugin Name: PhotoQ
 Version: 1.6.1
 Plugin URI: http://www.whoismanu.com/blog/
 Description: Adds queue based photo management and upload capability to WordPress. 
 Author: M. Flury
 Author URI: http://www.whoismanu.com
 */


//photoq should only be loaded in the admin section to save webserver cpu
//on ajax calls is_admin() seems not to be ready yet -> we load photoq also whenever we do ajax.

if(( defined('DOING_AJAX') && DOING_AJAX ) || is_admin() ){
	if (! defined('PHOTOQ_DEBUG_LEVEL')) {
		//define the debug levels
		define('PHOTOQ_DEBUG_OFF', '0');
		define('PHOTOQ_SHOW_PHP_ERRORS', '1');
		define('PHOTOQ_LOG_MESSAGES', '2');
	
		//set the debug level here
		define('PHOTOQ_DEBUG_LEVEL', PHOTOQ_DEBUG_OFF);
	}
	
	//set displaying of error messages
	if(PHOTOQ_DEBUG_LEVEL >= PHOTOQ_SHOW_PHP_ERRORS){
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL ^ E_NOTICE);
	}
	
	set_time_limit(0);
	
	
	if (!class_exists("PhotoQ")) {
		//convert backslashes (windows) to slashes
		$cleanPath = str_replace('\\', '/', dirname(__FILE__));
		define('PHOTOQ_PATH', $cleanPath.'/');
		require_once(PHOTOQ_PATH.'classes/PhotoQ.php');
	}
	
	
	if (class_exists("PhotoQ")) {
	
		$photoq = new PhotoQ();
		
		/*in the case where batch upload is enabled, we have to override the pluggable functions
		 responsible for reading auth cookie, so that they allow login info to be submitted via post 
		 or get request. The reason is that the upload request comes from the flash script which doesn't 
		 have access to the user, password cookie. Try to minimize this, so only do it when something is uploaded.
		 */
		if ( !function_exists('wp_validate_auth_cookie') && $photoq->_oc->getValue('enableBatchUploads') && isset($_POST['batch_upload']) ) :
		function wp_validate_auth_cookie($cookie = '', $scheme = 'auth') {
			
			//here starts the part that is new -- get cookie value from request, model taken from media.php
			global $photoq;
			if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
				$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
			elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
				$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
			//here ends the part that is new -- the rest is copy paste from pluggable.php
			
			if ( empty($cookie) ) {
				if ( is_ssl() ) {
					$cookie_name = SECURE_AUTH_COOKIE;
					$scheme = 'secure_auth';
				} else {
					$cookie_name = AUTH_COOKIE;
					$scheme = 'auth';
				}
	
				if ( empty($_COOKIE[$cookie_name]) )
				return false;
				$cookie = $_COOKIE[$cookie_name];
			}
	
			$cookie_elements = explode('|', $cookie);
			if ( count($cookie_elements) != 3 )
			return false;
	
			list($username, $expiration, $hmac) = $cookie_elements;
	
			$expired = $expiration;
	
			// Allow a grace period for POST and AJAX requests
			if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
			$expired += 3600;
	
			// Quick check to see if an honest cookie has expired
			if ( $expired < time() )
			return false;
	
			$key = wp_hash($username . '|' . $expiration, $scheme);
			$hash = hash_hmac('md5', $username . '|' . $expiration, $key);
	
			if ( $hmac != $hash )
			return false;
	
			$user = get_userdatabylogin($username);
			if ( ! $user )
			return false;
	
			return $user->ID;
		}
		endif;
	
		
	}//if (class_exists("PhotoQ")) {
}//if(( defined('DOING_AJAX') && DOING_AJAX ) || is_admin() ){





?>
