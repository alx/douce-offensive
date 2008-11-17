<?php
class PhotoQHelper
{

	function createDir($path)
	{
		$created = true;
		if (!file_exists($path)) {
			$created = PhotoQHelper::recursiveMakeDir($path, 0777);
		}
		return $created;
	}
	
	function removeDir($path)
	{
		$removed = true;
		if (file_exists($path)) {
			$removed = rmdir($path);
		}
		return $removed;
	}
	
	/**
	 * Returns matching content from a directory.
	 *
	 * @param string $path			path of the directory.
	 * @param string $matchRegex	regex a filename should match.
	 * @return array	path to files that matched
	 */
	function getMatchingDirContent($path, $matchRegex)
	{
		$result = array();
		if ( $handle = opendir($path) ) {
			while (false !== ($file = readdir($handle))) {
				if (preg_match($matchRegex, $file)) { //only include files matching regex
					array_push($result, $path.$file);
				}
			}
			closedir($handle);
		}
		return $result;
	}
	
	/**
	 * The mkdir() recursive flag doesn't work under php4. So we have to
	 * define our own function to create dirs recursively.
	 *
	 * @param string $pathname
	 * @param int $mode
	 * @return boolean
	 */
	function recursiveMakeDir($pathname, $mode)
	{
		is_dir(dirname($pathname)) || PhotoQHelper::recursiveMakeDir(dirname($pathname), $mode);
		return is_dir($pathname) || @mkdir($pathname, $mode);
	}

	
	/**
	 * Remove directory and all its content recursively.
	 *
	 * @param string $filepath
	 * @return boolean
	 */
	function recursiveRemoveDir($filepath)
	{
		if (is_dir($filepath) && !is_link($filepath))
		{
			if ($dh = opendir($filepath))
			{
				while (($sf = readdir($dh)) !== false)
				{
					if ($sf == '.' || $sf == '..')
					{
						continue;
					}
					if (!PhotoQHelper::recursiveRemoveDir($filepath.'/'.$sf))
					{
						$rmError = new PhotoQErrorMessage($filepath.'/'.$sf.' could not be deleted.');
						$rmError->show();
					}
				}
				closedir($dh);
			}
			return rmdir($filepath);
		}
		if(file_exists($filepath))
			return unlink($filepath);
		else
			return false;	
	}

	
	/**
	 * Moves $oldfile to $newfile, overwriting $newfile if it exists. We have to use
	 * this function instead of the builtin PHP rename because the latter does not work as expected
	 * on Windows (cf comments @ http://ch2.php.net/rename). Returns TRUE on success, FALSE on failure.
	 *
	 * @param string $oldfile The path to the file to be moved
	 * @param string $newfile The path where $oldfile should be moved to.
	 *
	 * @return boolean TRUE if file is successfully moved
	 *
	 * @access public
	 */
	function moveFile($oldfile,$newfile)
	{
		if (!rename($oldfile,$newfile)) {
			if (copy ($oldfile,$newfile)) {
				unlink($oldfile);
				return TRUE;
			}
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * PHP built-in array_combine only works for PHP5. 
	 * This function should do more or less the same and
	 * also work with PHP4.
	 *
	 * @param array $keys
	 * @param array $values
	 * @return array
	 */
	function arrayCombine($keys, $values) {
		$out = array();
		 
		$keys = array_values($keys);
		$values = array_values($values);
		 
		foreach( $keys as $index => $key ) {
			$out[(string)$key] = $values[$index];
		}
		 
		return $out;
	}
	
	/**
	 * PHP built-in pathinfo() does not have filename field
	 * under PHP4. This is a fix for this.
	 *
	 * @param string $path
	 * @return array
	 */
	function pathInfo($path){
		$pathParts = pathinfo($path);
		// if php4
		if(!isset($pathParts['filename'])){
			$pathParts['filename'] = substr($pathParts['basename'], 0,strpos($pathParts['basename'],'.'));
		}
		return $pathParts;
	}
	
	/**
	 * Converts absolute path to relative url
	 *
	 * @param string $path
	 * @return string
	 */
	function getRelUrlFromPath($path)
	{	
		//convert backslashes (windows) to slashes
		$abs = str_replace('\\', '/', ABSPATH);
		$path = str_replace('\\', '/', $path);
		
		//remove ABSPATH
		$relUrl = str_replace($abs, '', trim($path));
		//remove slashes from beginning
		//echo "<br/> relURl: $relUrl </br>";
		return preg_replace('/^\/*/', '', $relUrl);
	}
	
	/**
	 * Reduces multidimensional array to single dimension.
	 *
	 * @param array $in
	 * @return array
	 */
	function flatten($in){
		$out = array();
		if(is_array($in)){
			foreach ($in as $key => $value){
				if(is_array($value)){
					unset($in[$key]);
					$out = array_merge($out,PhotoQHelper::flatten($value));
				}else
				$out[$key] = $value;
			}
		}
		return $out;
	}
	
	
	/**
	 * Gets an array of all the <$tag>content</$tag> tags contained in $string.
	 *
	 * @param string $tag
	 * @param string $string
	 * @return array
	 */
	function getHTMLTags($tag, $string){
		$result = array();
		$bufferedOpen = array();
		$offset = 0;
		$nextClose = strpos($string, "</$tag>", $offset);
		while($nextClose !== false){
			$nextOpen = strpos($string, "<$tag", $offset);
			$offset = $nextClose;
			while($nextOpen < $nextClose && $nextOpen !== false){
				array_push($bufferedOpen,$nextOpen);
				$nextOpen = strpos($string, "<$tag", $nextOpen+1);
			}
			//we got a pair
			$start = array_pop($bufferedOpen);
			array_push($result,substr($string,$start,$nextClose-$start+strlen($tag)+3));
			$nextClose = strpos($string, "</$tag>", $nextClose+1);
		}
		return $result;
	}
	
	/**
	 * Get the maximum allowable file size in KB from php.ini
	 *
	 * @return integer the maximum size in kilobytes
	 */
	function getMaxFileSizeFromPHPINI()
	{
		$max_upl_size = strtolower( ini_get( 'upload_max_filesize' ) );
		$max_upl_kbytes = 0;
		if (strpos($max_upl_size, 'k') !== false)
		$max_upl_kbytes = $max_upl_size;
		if (strpos($max_upl_size, 'm') !== false)
		$max_upl_kbytes = $max_upl_size * 1024;
		if (strpos($max_upl_size, 'g') !== false)
		$max_upl_kbytes = $max_upl_size * 1024 * 1024;
	
		return $max_upl_kbytes;
	}
	
	
	/**
	 * Logs message $msg to a file if debbugging is enabled.
	 *
	 * @param string $msg   The message to be logged to the file.
	 *
	 * @access public
	 */
	function debug($msg)
	{
		if(PHOTOQ_DEBUG_LEVEL >= PHOTOQ_LOG_MESSAGES){
			require_once realpath(PHOTOQ_PATH.'lib/Log-1.9.11/Log.php');
			$conf = array('mode' => 0777, 'timeFormat' => '%X %x');
			$logger = &Log::singleton('file', PHOTOQ_PATH.'log/out.log', '', $conf);
			$logger->log($msg);
		}	
	}
	
	
	
	/**
	 * Checks whether a post is a photo post. A post is considered a photopost if the same image
	 * appears in the content and the excerpt part
	 *
	 * @param object $post The post to be checked
	 * @return boolean True if the post is photo post
	 * @access public
	 */
	/*function isPhotoPost($post)
	{
		$imgTags = $this->getHtmlTags($post->post_excerpt, "img");
		if(!count($imgTags)){
			return false;
		}
		foreach($imgTags as $thumb){
			$attributes = $this->getAttributesFromHtmlTag($thumb);
			$thumbName = basename($attributes['src']);
			$expectedImgName = basename($this->getImgPathFromThumbPath($attributes['src'], $post));
			if($thumbName == $expectedImgName)
			return false;//it didn't have the thumb_identifier
	
			//now check whether the content part has an image tag with $expectedImgName
			if(!$this->getImgTagByName($post->post_content, $expectedImgName))
			return false;
		}
	
		return true;
	}*/
	
	
	/**
	 * Extracts attribute - value pairs from HTML tags.
	 *
	 * @param string $tag The tag to get the pairs from.
	 * @return Array associative array containing attribute value pairs.
	 * @access public
	 *
	 */
	/*function getAttributesFromHtmlTag($tag)
	{
		$result = array();
	
		if(PhotoQHelper::isSingleHtmlTag($tag)){
			//get and return the attribute->value pairs
			preg_match_all('/(\w+)=\"([^\"]+)\"/', $tag, $attributes, PREG_SET_ORDER);
			foreach ($attributes as $attr){
				$result[$attr[1]] = $attr[2];
			}
		}
		return $result;
	}*/
	
	
	/**
	 * Checks whether the string contains one single HTML tag and nothing else. Single HTML tag if
	 * one single opening bracket at beginning and one single closing bracket that is at the end of
	 * the string.
	 *
	 * @param string $string The string to be checked.
	 * @return boolean True if the string contains one single HTML tag.
	 * @access public
	 *
	 */
	/*function isSingleHtmlTag($string)
	{
		return preg_match('/^<[^<^>]*>$/',$string);
	}*/
	
	/**
	 * Returns all HTML tags of a given type found in a string.
	 *
	 * @param string $string The string to be searched.
	 * @param string $tag The type of tag to look for.
	 * @return Array array containing the img tags.
	 * @access public
	 *
	 */
	/*function getHtmlTags($string, $tag)
	{
		preg_match_all("/<$tag [^<^>]*>/", $string, $foundTags, PREG_PATTERN_ORDER);
		return $foundTags[0];
	}*/
	
	/**
	 * Constructs image path from thumb paths.
	 *
	 * @param string $thumbPath The path of the thumbnail.
	 * @param object $post The post to which the thumbnail belongs.
	 * @return mixed string path of the corresponding image if transformation
	 * succeeds, null if we don't know how to do the backwards transfromation.
	 *
	 * @access public
	 *
	 */
	/*function getImgPathFromThumbPath($thumbPath, $post)
	{
		$imgPath = preg_replace("/.".$this->_oc->getThumbIdentifier()."/",'',$thumbPath);
		if ($thumbPath == $this->getThumbPathFromImgPath($imgPath)){
			return $imgPath;
		}
		else{
			//there was a non standard transformation, we have to look through the
			//whole post and match the image path via the backwards transformation
			$imgTags = $this->getHtmlTags($post->post_content, "img");
			foreach($imgTags as $img){
				$attributes = $this->getAttributesFromHtmlTag($img);
				$imgPath = $attributes['src'];
				if ( $thumbPath == $this->getThumbPathFromImgPath($imgPath)){
					return $imgPath;
				}
			}
			return null;
		}
	}*/
	
	
	/*returns the name of the thumbnail path from the image path*/
	/*function getThumbPathFromImgPath($imgPath) {
		// If no filters change the filename, we'll do a default transformation.
		$thumb = preg_replace('!(\.[^.]+)?$!', ".".$this->_oc->getThumbIdentifier() . '$1', basename($imgPath), 1);
		return str_replace(basename($imgPath), $thumb, $imgPath);
	}*/
	
	/**
	 * Looks in $string whether it finds an img tag with an image of filename $name.
	 *
	 * @param string $string look for img tag in this string.
	 * @param string $name the name to look for.
	 * @return mixed null if not found, img tag if found.
	 * @access public
	 *
	 */
	/*function getImgTagByName($string, $name)
	{
		$imgTags = $this->getHtmlTags($string, "img");
		foreach($imgTags as $img){
			$attributes = $this->getAttributesFromHtmlTag($img);
			$imgName = basename($attributes['src']);
			if($imgName == $name){
				return $img;
			}
		}
	
		return null;
	}*/
	
	/**
	 * Checks whether an <img> tag is part of a link, i.e. a child of an <a> tag.
	 *
	 * @param string $string The string in which to check
	 * @param string $imgTag The <img> tag to check
	 * @return boolean True if it is part of a link, False if not
	 * @access public
	 *
	 */
	/*function isPartOfLink($string,$imgTag)
	{
		foreach($this->getAllLinks($string) as $link){
			if(preg_match("#$imgTag#",$link))
			return true;
		}
		return false;
	}*/
	
	/**
	 * Returns all links (<a href="bla.html">bla bla</a>) contained in a string.
	 *
	 * @param string $string	The string in which to look for links.
	 * @return Array 			Array of links found
	 * @access public
	 *
	 */
	/*function getAllLinks($string)
	{
		preg_match_all('#<a.*?</a>#', $string, $matches);
		return $matches[0];
	}*/
	
	
	/**
	 * Transforms an associative array of attributes to string of attribute="value" pairs.
	 *
	 * @param Array $attributes the attributes to be converted
	 * @return string the string of attribute="value" pairs.
	 * @access public
	 *
	 */
	/*function attibutesToString($attributes){
		$result = '';
		foreach($attributes as $attribute => $value){
			$result .= $attribute . '="' . $value . '" ';
		}
		return $result;
	}*/
	
	
	/**
	 * This is a filter hooked into the the_content WordPress hook. Replaces image tags with a link
	 * to corresponding image, the link text being the thumbnail version. This enables scripts like
	 * Lightbox and Shutter Reloaded.
	 *
	 * @param string $content     The content of the post as it is stored in the WordPress Database.
	 *
	 * @returns string            The content with images replaced with image links.
	 * @access public
	 */
	/*function replaceImagesWithImageLink($content)
	{
		global $post;
		if($this->isPhotoPost($post)){
				
			$thumbs = $this->getHtmlTags($post->post_excerpt, "img");
				
			foreach($thumbs as $thumb){
				$thumbAttributes = $this->getAttributesFromHtmlTag($thumb);
				$expectedImgName = basename($this->getImgPathFromThumbPath($thumbAttributes['src'],$post));
				
				if($img = $this->getImgTagByName($post->post_content, $expectedImgName)){
					if(!$this->isPartOfLink($post->post_content,$img)){
						//build image link
						$imgAttributes = $this->getAttributesFromHtmlTag($img);
						$imgLink = '<a '. stripslashes(html_entity_decode($this->_oc->getValue('imgLinksAttributes'))) . ' href="'.$imgAttributes['src'].'"><img ';
						$imgLink .= $this->attibutesToString($thumbAttributes);
						$imgLink .= '/></a>';
						//replace image tag with img link
						$content = preg_replace('#'.preg_quote($img, '#').'#',$imgLink,$content);
					}
				}
			}
		}
		return $content;
	}
	
	function listAllPhotosWithoutParent($currentName, $currentParent){
		global $wpdb;
		//check whether this one has children
		if($children = $this->getPhotoChildren($currentName)){
			echo "";
		}else{
			//get possible parent photos
			$results = $wpdb->get_results("
			SELECT
			q_imgname, q_title
			FROM
			$this->QUEUE_TABLE
			WHERE
			q_parent = ''
					");
			if($results){
				echo 'Parent Photo: <select name="img_parent[]">';
				echo '<option value="">None</option>';
	
				foreach($results as $parent)
				if($parent->q_imgname != $currentName){
					echo '<option';
					if($parent->q_imgname == $currentParent)
					echo ' selected="selected"';
					echo ' value="'.$parent->q_imgname.'">'. $parent->q_title.'</option>';
				}
				echo '</select>';
			}
		}
	}
	
	function getPhotoChildren($currentName){
		global $wpdb;
		$children = $wpdb->get_results("
		SELECT
		*
		FROM
		$this->QUEUE_TABLE
		WHERE
		q_parent = '$currentName'
				");
	
		return $children;
	}*/
	
	/**
	 * This is a filter hooked into the the_content WordPress hook. Allows to modify the_content on
	 * the fly, e.g., replace image tags with a link to corresponding image, the link text being the 
	 * thumbnail version. This enables scripts like Lightbox and Shutter Reloaded.
	 *
	 * @param string $content     The content of the post as it is stored in the WordPress Database.
	 *
	 * @returns string            The content with images replaced with image links.
	 * @access public
	 */
	/*function modifyContentOnTheFly($content)
	{
		return $this->_modifyOnTheFly($content);
	}*/
	
	
	/**
	 * This is a filter hooked into the the_excerpt WordPress hook. Allows to modify the_excerpt on
	 * the fly, e.g., replace image tags with a link to corresponding image, the link text being the 
	 * thumbnail version. This enables scripts like Lightbox and Shutter Reloaded.
	 *
	 * @param string $excerpt     The excerpt of the post as it is stored in the WordPress Database.
	 *
	 * @returns string            The modified excerpt e.g. with images replaced with image links.
	 * @access public
	 */
	/*function modifyExcerptOnTheFly($excerpt)
	{
		return $this->_modifyOnTheFly($excerpt, 'excerpt');
	}*/
	
	
	/**
	 * Both of the above filter functions call this one to do the work.
	 *
	 * @param string $data		data to be modified, either from excerpt or content
	 * @param string $viewName	indicates whether we are dealing with excerpt or content
	 * @return string $data the modified data
	 * @access private
	 */
	/*function _modifyOnTheFly($data, $viewName = 'content')
	{
		global $post;
		
		if($this->isPhotoPost($post)){
			$photo = new PhotoQPublishedPhoto($post->ID, $post->title);
			$data = $photo->generateContent($viewName);
		}
		return $data;
	}*/
	
	
}


/**
 * No exceptions in PHP4, so let's try to have at least 
 * some level of error handling through this class
 *
 */
class PhotoQStatusMessage
{

	/**
	 * The string message.
	 *
	 * @var string
	 * @access private
	 */
	var $_msg;
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQStatusMessage($msg = '')
	{
		$this->__construct($msg);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($msg = '')
	{
		$this->_msg = $msg;	
	}
	
	/**
	 * Whether the message denotes an error or not.
	 *
	 * @access public
	 * @return boolean
	 */
	function isError()
	{
		return false;		
	}
	
	/**
	 * Print the message to screen.
	 *
	 * @access public
	 */
	function show()
	{
 		echo '<div class="updated fade">';
		echo "<p>$this->_msg</p>";
		echo '</div>';
	}
	
	/**
	 * Getter for the message string.
	 *
	 * @access public
	 * @return string
	 */
	function getMsg()
	{
		return $this->_msg;
	}
	
}

/**
 * This message can be returned if there was an error.
 *
 */
class PhotoQErrorMessage extends PhotoQStatusMessage
{
	function isError()
	{
		return true;		
	}

	function show()
	{
		echo '<div class="error">';
		echo $this->getMsg();
		echo '</div>';
	}
}



/**
 * Helper class to implement Singleton pattern. Instantiate objects like
 * $object =& PhotoQSingleton::getInstance('ClassName');
 *
 */
class PhotoQSingleton
{
	/**
	 * implements the 'singleton' design pattern.
	 */
	function getInstance ($class)
	{
		static $instances = array();  // array of instance names

		if (!array_key_exists($class, $instances)) {
			// instance does not exist, so create it
			$instances[$class] =& new $class;
		}
		$instance =& $instances[$class];
		return $instance;
	}
} // singleton

?>
