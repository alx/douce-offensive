<?php

class PhotoQPhoto
{
	
	/**
	 * The name of the custom/meta field used for the photo description
	 *
	 * @var string
	 * @access private
	 * 
	 */
	var $_descrFieldName = 'photoQDescr';
	var $_pathFieldName = 'photoQPath';
	var $_exifFullFieldName = 'photoQExifFull';
	var $_exifFieldName = 'photoQExif';
	var $_sizesFieldName = 'photoQImageSizes';
	
	var $_oc;
	var $_db;
	var $_sizes = array();
	var $_path;
	var $_width;
	var $_height;
	var $_yearMonthDir;	
	var $title;
	var $descr;
	var $imgname;
	var $exif;
	var $niceExif;
		
	//var $logger;
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQPhoto($imgname, $title, $descr, $exif, $path = '')
	{
		$this->__construct($imgname, $title, $descr, $exif, $path);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($imgname, $title, $descr, $exif, $path = '')
	{
		$this->imgname = $imgname;
		$this->title = $title;
		$this->descr = $descr;
		$this->exif = maybe_unserialize($exif);
		
		
		//$conf = array('mode' => 0777, 'timeFormat' => '%X %x');
		//$this->logger = &Log::singleton('file', PHOTOQ_PATH.'log/test.log', '', $conf);
		
		$this->_oc =& PhotoQSingleton::getInstance('PhotoQOptionController');
		$this->_db =& PhotoQSingleton::getInstance('PhotoQDB');
		
		$this->niceExif = PhotoQExif::getFormattedExif($this->exif,$this->_oc->getValue('exifTags'));
		
		if(empty($path))
			$this->_path = $this->_oc->getQDir() . $this->imgname;
		else
			$this->_path = $path;
		
		
		//set original width and height
		$imageAttr = getimagesize($this->_path);
		$this->_width = $imageAttr[0];
		$this->_height = $imageAttr[1];
		
		//add all the image sizes
		foreach (array_keys($this->_oc->getValue('imageSizes')) as $sizeName){
			$this->_sizes[$sizeName] = PhotoQImageSize::createInstance($sizeName, $this->imgname, $this->_yearMonthDir, $this->_width, $this->_height);
		}
		//add the original
		$this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER] = PhotoQImageSize::createInstance($this->_oc->ORIGINAL_IDENTIFIER, $this->imgname, $this->_yearMonthDir, $this->_width, $this->_height);
		
	}
	
	/**
	 * Deletes this photo from the server.
	 *
	 * @return object PhotoQStatusMessage
	 */
	function delete()
	{
		//remove from server
		$deleted = true;
		if(file_exists($this->_path))
			$deleted = unlink($this->_path);
		if(!$deleted)
			$status = new PhotoQErrorMessage(__("Could not delete photo $this->imgname from server. Please delete manually."));
		else
			$status = new PhotoQStatusMessage(__('Entry successfully removed from queue. Corresponding files deleted from server.'));
		return $status;
	}
	
	
	function generateImgTag($sizeName, $class)
	{				
		return '<img width="'.$this->_sizes[$sizeName]->getScaledWidth().'" height="'.$this->_sizes[$sizeName]->getScaledHeight().'" alt="'.$this->title.'" src="'.$this->_sizes[$sizeName]->getUrl().'" class="'.$class.'" />';
	}
	
	function generateImgLink($sourceSizeName, $targetSizeName, $attributes, $class)
	{
		return '<div '. $attributes . ' url="'.$this->_sizes[$targetSizeName]->getUrl().'"><img width="'.$this->_sizes[$sourceSizeName]->getScaledWidth().'" height="'.$this->_sizes[$sourceSizeName]->getScaledHeight().'" alt="'.$this->title.'" src="'.$this->_sizes[$sourceSizeName]->getUrl().'" class="'.$class.'" /><script type="text/javascript" charset="utf-8">jQuery.preloadImages("'.$this->_sizes["main"]->getUrl().'");</script></div>';
	}
	
	/**
	 * Generates the data stored in the_content or the_excerpt.
	 *
	 * @param string $viewName the name of the view to generate (content or excerpt).
	 * @return string	the data to be stored.
	 */
	function generateContent($viewName = 'content')
	{
		switch($this->_oc->getValue($viewName . 'View-type')){

			case 'single':
				$singleSize = $this->_oc->getValue($viewName . 'View-singleSize');
				//if($singleSize != 'main')
				$data = $this->generateImgTag($singleSize, "photoQ$viewName photoQImg");
				break;

			case 'imgLink':
				$sourceSize = $this->_oc->getValue($viewName . 'View-imgLinkSize');
				$targetSize = $this->_oc->getValue($viewName . 'View-imgLinkTargetSize');
				$data = $this->generateImgLink($sourceSize, $targetSize,
					stripslashes(html_entity_decode($this->_oc->getValue($viewName . 'View-imgLinkAttributes'))),
					"photoQ$viewName photoQLinkImg"
				);
				break;
		}
		
		if($viewName == 'content'){
			if($this->_oc->getValue('inlineDescr'))
				//leave this on separate line or wpautop() will mess up, strange but true...
				$data .= '
				<div class="'.$this->_descrFieldName.'">' . $this->descr . '</div>';
			if($this->_oc->getValue('inlineExif'))
				$data .= $this->niceExif;
		}
		
		return $data;
			
	}
	
	function generateSizesField()
	{
		$sizeFieldData = array();
		foreach($this->_sizes as $size){
			$imgTag = $this->generateImgTag($size->getName(), "PhotoQImg");
			$imgUrl = $size->getUrl();
			$imgPath = $size->getPath();
			$imgWidth = $size->getScaledWidth();
			$imgHeight = $size->getScaledHeight();
			$sizeFieldData[$size->getName()] = compact('imgTag', 'imgUrl', 'imgPath', 'imgWidth', 'imgHeight');
		}	
		return $sizeFieldData;
	}
	
	
	
	/**
	 * Rebuild the downsized version for a given image size.
	 *
	 * @param object PhotoQImageSize $size
	 * @return boolean
	 */
	function rebuild($size, $moveOriginal = true){
		$status = $size->createPhoto($this->_path, $moveOriginal);
		if($status->isError()){//an error occurred
			$status->show();
			$this->cleanUpAfterError();
			return false;
		}
		return true;
	}
	
	function cleanUpAfterError(){
		//move back original if it has been moved already
		$oldPath = $this->_oc->getQDir() . $this->imgname;
		if (!file_exists($oldPath) && file_exists($this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER]->getPath()))
			PhotoQHelper::moveFile($this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER]->getPath(), $oldPath);
		
		//remove any resized images that have been created unless a corresponding original image exists
		
		if(!file_exists($this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER]->getPath())){
			foreach($this->_sizes as $size){
				$size->deleteResizedPhoto();
			}
		}
	}
	
	/**
	 * Rebuild downsized version of an image given the name of the downsized version.
	 *
	 * @param string $sizeName
	 * @return boolean
	 */
	function rebuildByName($sizeName){
		$size = $this->_sizes[$sizeName];
		return $this->rebuild($this->_sizes[$sizeName]);
	}
	
	
}

class PhotoQQueuedPhoto extends PhotoQPhoto
{
	 
	var $slug; 
	var $tags; 
	var $edited; 
	var $id;
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQQueuedPhoto($id, $imgname, $title, $descr, $slug, 
						$tags, $exif, $edited)
	{
		$this->__construct($id, $imgname, $title, $descr, $slug, 
						$tags, $exif, $edited);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($id, $imgname, $title, $descr, $slug, 
						$tags, $exif, $edited)
	{
		
		$this->id = $id;
		$this->slug = $slug;
		$this->tags = $tags;
		$this->edited = $edited;
		
		$this->_yearMonthDir = mysql2date('Y_m', current_time('mysql')) . "/";
		
		
		parent::__construct($imgname, $title, $descr, $exif);
		
	}
	
	
	
	/**
	 * Publish the Photo. Creates the resized images, inserts post data into database
	 *
	 * @return integer	The ID of the post created.
	 */
	function publish($timestamp = 0)
	{
		//create the resized images and move them into position
		foreach($this->_sizes as $size){
			if(!$this->rebuild($size)){//an error occurred
				return 0;
			}
		}
		
		//generate the post data and add it to database
		$postData = $this->_generatePostData($timestamp);
		if (!$postID = wp_insert_post($postData)) { //post did not succeed
			$this->cleanUpAfterError();
			return 0;
		}
		
		//insert description
		add_post_meta($postID, $this->_descrFieldName, $this->descr, true);
		
		//insert full exif
		add_post_meta($postID, $this->_exifFullFieldName, $this->exif, true);
		
		//insert formatted exif
		add_post_meta($postID, $this->_exifFieldName, $this->niceExif, true);
		
		//insert sizesFiled
		add_post_meta($postID, $this->_sizesFieldName, $this->generateSizesField(), true);
		
		//add path variable
		add_post_meta($postID, $this->_pathFieldName, $this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER]->getPath(), true);
	
		//handle the other fields
		$fields = $this->_db->getAllFields();
		foreach ($fields as $field) {
			$fieldValue = $this->_db->getFieldValue($this->id, $field->q_field_id);
			add_post_meta($postID, $field->q_field_name, $fieldValue, true);
		}
			
		return $postID;
					
	}
	
	
	
	
	
	
	
	function _generatePostData($timestamp){
		
		$post_author = $this->_oc->getValue('qPostAuthor');
		$post_status = 'publish';
		$post_title = $this->title;
	
		//if a timestamp is given we set the post_date
		if($timestamp)
			$post_date = gmdate( 'Y-m-d H:i:s' , $timestamp );
		
		//the slug
		$post_name =  $this->slug;
	
		//the tags
		$tags_input =  $this->tags;
	
		//category stuff
		$post_category = $this->_db->getCategoriesByImgId($this->id);
	
		// Make sure we set a valid category
		if (0 == count($post_category) || !is_array($post_category)) {
			$post_category = array($this->_oc->getValue('qPostDefaultCat'));
		}
	
		$post_content = $this->generateContent();
		$post_excerpt = $this->generateContent('excerpt');
			
		
		$postData = compact('post_content','post_category','post_title','post_excerpt','post_name','post_author', 'post_status', 'tags_input', 'post_date');
		//to safely insert values into db
		$postData = add_magic_quotes($postData);
		
		
		return $postData;
		
	}
	
	


}


class PhotoQPublishedPhoto extends PhotoQPhoto
{
	
	var $_postID;
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQPublishedPhoto($postID, $title, $descr = '', $exif = '', $path = '')
	{
		$this->__construct($postID, $title, $descr, $exif, $path);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($postID, $title, $descr = '', $exif = '', $path = '')
	{
		if(empty($path)) $path = get_post_meta($postID, $this->_pathFieldName, true);
		if(empty($descr)) $descr = get_post_meta($postID, $this->_descrFieldName, true);
		if(empty($exif)) $exif = get_post_meta($postID, $this->_exifFullFieldName, true);
		
		//read ymd and imgname from path
		$imgname = basename($path);
		$this->_yearMonthDir = array_pop(explode('/', dirname($path))) . "/";
		$this->_postID = $postID;
		parent::__construct($imgname, $title, $descr, $exif, $path);
	}
	
	/**
	 * For published photos we also delete the thumbs.
	 *
	 */
	function delete()
	{
		foreach($this->_sizes as $size){
			$size->deleteResizedPhoto();
		}
		parent::delete();
	}
	
	/**
	 * Updates the content of an alreay published photo post.
	 *
	 * @return integer the ID of the post
	 */
	function updatePost()
	{
		$ID = $this->_postID;
		$post_content = $this->generateContent();
		$post_excerpt = $this->generateContent('excerpt');
		$postData = compact('ID', 'post_content', 'post_excerpt');
		$postData = add_magic_quotes($postData);
		$res = wp_update_post($postData);
		//kill revisions
		$this->_db->removeRevisions($ID);	
		return $res;
	}
	
	/**
	 * Update the path replacing $old by $new in path meta field.
	 *
	 * @param string $old
	 * @param string $new
	 */
	function updatePath($old, $new)
	{
		$this->_path = str_replace($old, $new, $this->_path);
		update_post_meta($this->_postID, $this->_pathFieldName, $this->_path);
	}
	
	/**
	 * Updates the formatted exif of an already published photo post.
	 *
	 */
	function updateExif()
	{
		update_post_meta($this->_postID, $this->_exifFieldName, $this->niceExif);
	}
	
	/**
	 * Updates the field containing info on image sizes.
	 *
	 */
	function updateSizesField()
	{
		update_post_meta($this->_postID, $this->_sizesFieldName, $this->generateSizesField());
	}
	
	
	
	/**
	 * Called whenever a photo post is edited and saved in the wordpress editor. If the content
	 * changed, we sync the change to the description custom field as well.
	 * There is no wordpress filter for updated content that passes the post id along. Thus our
	 * only option was to use this action that is called on updates. The problem is that we also want
	 * to call update at the end of this function to make sure the user didn't delete or change 
	 * e.g. any of the images. Now this would create a loop so we have to break it with the first
	 * if. this function is thus executed twice whenever a post is updated. Should the wp api change
	 * we would prefer to use a filter and return $this->generateContent().
	 *
	 * @param string $content
	 */
	function syncContent($content)
	{
		//needed to break loop see function description.
		if($this->generateContent() != $content){
			if($this->_oc->getValue('inlineDescr')){
				//we are now trying to find the description
				$descr = $this->getInlineDescription($content);
				//the rest is the description
				if($descr){
					$this->descr = $descr;
					//sync it with the field
					update_post_meta($this->_postID, $this->_descrFieldName, $this->descr);
				}
			}
			//and update post so everything looks nice.
			$this->updatePost();
		}
	}

	
	/**
	 * Our own little parser as there doesn't seem to be a reasonable one that works
	 * with both PHP4 and PHP5. A bit cumbersome and certainly not nice but it seems
	 * to work.
	 *
	 * @param string $content
	 * @return string
	 */
	function getInlineDescription($content, $className = 'photoQDescr'){
		$descr = '';
		$photoQDescrTagsInnerHTML = array(); 
		$pTags = PhotoQHelper::getHTMLTags('div', $content);
		PhotoQHelper::debug('pTags: ' . print_r($pTags,true));
		
		foreach($pTags as $pTag){
			$matches = array();
			$found = preg_match('#^(<div.*?class="'.$className.'".*?>)#',$pTag,$matches);
			if($found){
				//remove the p start and end tag, the rest is the description.
				array_push($photoQDescrTagsInnerHTML, str_replace($matches[1],'',substr($pTag,0,strlen($pTag)-6)));
			}
		}
		
		PhotoQHelper::debug('photoQDescrTagsInnerHTML: ' . print_r($photoQDescrTagsInnerHTML,true));
		
		//if we have more than one p.photoQDescr tag, it means that there were several
		//lines created in the editor -> wrap each one with a p tag.
		$numDescrTags = count($photoQDescrTagsInnerHTML);
		if($numDescrTags == 1)
			$descr = $photoQDescrTagsInnerHTML[0];
		else
			for ($i = 0; $i < $numDescrTags; $i++){
				if($photoQDescrTagsInnerHTML[$i] !== '')
					$descr .= "<p>$photoQDescrTagsInnerHTML[$i]</p>";
			}
		
		PhotoQHelper::debug('descr:' . $descr);
		return $descr;
	}
	
	
	/**
	 * Replaces the description field with the inlined description from the_content.
	 *
	 * @param string $content
	 * @return string	The same $content that was input, filter doesn't affect $content.
	 */
	function syncField($content)
	{
		//we are now trying to find the description
		$descr = $this->getInlineDescription($content);
		if($descr){
			$this->descr = $descr;
			//sync it with the field
			update_post_meta($this->_postID, $this->_descrFieldName, $this->descr);
		}
		return $content;
	}

	
}

/**
 * A photo published under photoq < 1.5 that needs to be imported.
 *
 */
class PhotoQImportedPhoto extends PhotoQPublishedPhoto
{
	
	/**
	 * PHP4 type constructor
	 */
	function PhotoQImportedPhoto($postID, $title, $descr = '', $path = '')
	{
		$this->__construct($postID, $title, $descr, $path);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($postID, $title, $descr = '', $path = '')
	{
		if(empty($path)) $path = get_post_meta($postID, 'path', true);
		
		//correct the path value if needed. on windows machines we might
		//find ourselves with all backslashes removed
		$path = str_replace(ABSPATH, '', trim($path)); //try to remove standard abspath
		$absNoSlash = str_replace('\\', '', ABSPATH); //create the crippled abspath
		$path = str_replace($absNoSlash, '', trim($path)); //try to remove crippled abspath
		$path = ABSPATH . $path; //add correct abspath
		
		if(empty($descr)) $descr = get_post_meta($postID, 'descr', true);
		
		//if it is still empty, the descr was inlined, we need to get it back
		if(empty($descr)){
			//we are now trying to find the description
			$post = get_post($postID);
			$descr = $this->getInlineDescription($post->post_content, 'photo_description');
		}

		//get the exif information
		$exif = serialize(PhotoQExif::readExif($path));
		
		parent::__construct($postID, $title, $descr, $exif, $path);
	}
	
	
	
	
	
	function upgrade()
	{
		//create the resized images and move them into position
		foreach($this->_sizes as $size){
			if(!$this->rebuild($size, false)){//an error occurred
				return 0;
			}
		}
		
		//insert description
		add_post_meta($this->_postID, $this->_descrFieldName, $this->descr, true);
		
		//insert full exif
		add_post_meta($this->_postID, $this->_exifFullFieldName, $this->exif, true);
		
		//insert formatted exif
		add_post_meta($this->_postID, $this->_exifFieldName, $this->niceExif, true);
		
		//insert sizesFiled
		add_post_meta($this->_postID, $this->_sizesFieldName, $this->generateSizesField(), true);
		
		//add path variable
		add_post_meta($this->_postID, $this->_pathFieldName, $this->_sizes[$this->_oc->ORIGINAL_IDENTIFIER]->getPath(), true);
		
		//delete old descr and path fields
		delete_post_meta($this->_postID, 'descr');
		delete_post_meta($this->_postID, 'path');
		
		//update content and excerpt
		$this->updatePost();
	}
}

?>