<?php
/**
 * @package PhotoQ
 */


/**
 * The PhotoQ:: class is mainly a wrapper for the PhotoQ WordPress Photoblog Plugin.
 * By grouping everything inside this class, we prevent name clashes with built-in
 * WordPress functions and other WordPress plugins.
 *
 * @author  M.Flury
 * @package PhotoQ
 */
class PhotoQ
{

	/**
	 * The current version of PhotoQ
	 *
	 * @var string
	 * @access private
	 */
	var $_version = '1.5.3';
	
	/**
	 * ObjectController managing all the plugins options
	 * @var Object
	 * @access private
	 */
	var $_oc;

	/**
	 * Database object managing access to database
	 * @var Object
	 * @access private
	 */
	var $_db;
	
	/**
	 * The queue that uploaded photos
	 *
	 * @var object
	 * @access private
	 */
	var $_queue;

	/**
	 * Name of main photoq database table, holds posts in queue
	 * @var string
	 * @access public
	 */
	var $QUEUE_TABLE;

	/**
	 * Name of photoq database table holding meta field names
	 * @var string
	 * @access public
	 */
	var $QFIELDS_TABLE;

	/**
	 * Name of photoq database table relating posts in queue to categories
	 * @var string
	 * @access public
	 */
	var $QCAT_TABLE;

	/**
	 * Name of photoq database table relating posts in queue to meta fields
	 * @var string
	 * @access public
	 */
	var $QUEUEMETA_TABLE;


	/**
	 * PHP4 type constructor
	 */
	function PhotoQ()
	{
		$this->__construct();
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct()
	{

		global $wpdb;
		
		//load the helpers first so that we can start logging debug messages
		require_once(PHOTOQ_PATH.'classes/PhotoQHelpers.php');
		
		PhotoQHelper::debug('-----------start plugin-------------');
		// load text domain for localization
		load_plugin_textdomain('PhotoQ');

		// set names of database tables used and created by photoq
		$this->QUEUEMETA_TABLE = $wpdb->prefix."photoqmeta";
		$this->QUEUE_TABLE = $wpdb->prefix."photoq";
		$this->QFIELDS_TABLE = $wpdb->prefix."photoqfields";
		$this->QCAT_TABLE = $wpdb->prefix."photoq2cat";
		
		// import libraries
		require_once(PHOTOQ_PATH.'lib/ReusableOptions/OptionController.php');
		//import other PhotoQ classes
		require_once(PHOTOQ_PATH.'classes/PhotoQOptionController.php');
		require_once(PHOTOQ_PATH.'classes/PhotoQDB.php');
		require_once(PHOTOQ_PATH.'classes/PhotoQQueue.php');
		require_once(PHOTOQ_PATH.'classes/PhotoQPhoto.php');
		require_once(PHOTOQ_PATH.'classes/PhotoQExif.php');
		
		
		require_once(PHOTOQ_PATH.'classes/PhotoQImageSize.php');
		
		
		// setting up options
		$this->_oc =& PhotoQSingleton::getInstance('PhotoQOptionController');
		
		// setting up database
		$this->_db =& PhotoQSingleton::getInstance('PhotoQDB');
		
		
		$this->autoUpgrade();
		
		//creating queue
		$this->_queue =& PhotoQSingleton::getInstance('PhotoQQueue');
			
		// actions and filters are next

		// Insert the add_admin_pages() sink into the plugin hook list for 'admin_menu'
		add_action('admin_menu', array(&$this, 'add_admin_pages'));

		// function executed when a post is deleted
		add_action ( 'delete_post', array(&$this, 'cleanUp'));

		// the next two hooks are used to show a thumb in the manage post section
		add_filter('manage_posts_columns', array(&$this, 'addThumbToListOfPosts'));
		add_action('manage_posts_custom_column', array(&$this, 'insertThumbIntoListOfPosts'), 10, 2);

		//add_filter('the_content', array(&$this, 'modifyContentOnTheFly'));
		//add_filter('the_excerpt', array(&$this, 'modifyExcerptOnTheFly'));
		
		
		//this one is called whenever a post was edited. what we then do is to sync the content
		//and the custom fields. ideally we would like to do this via content_save_pre instead
		//of the edit_post action but unfortunately content_save_pre is called in db context when
		//a post is updated and in this context it doesn't pass the post_id.
		add_action('edit_post', array(&$this, 'syncContent'), 100, 2);
		
		//if inline descriptions are used, the description has to be edited in the wp editor
		//it the users still modifies the custom field, we put it back to what it was the next
		//time the editor is loaded (we have to do it like this because there are no hooks for
		//field updates in wordpress yet.
		if($this->_oc->getValue('inlineDescr'))
			add_action('content_edit_pre', array(&$this, 'syncField'), 100, 2);
		
		
		register_activation_hook(PHOTOQ_PATH . 'whoismanu-photoq.php', array(&$this, 'activatePlugin'));
		register_deactivation_hook(PHOTOQ_PATH . 'whoismanu-photoq.php', array(&$this, 'deactivatePlugin'));

		/*
		foreach( $_POST as $key => $value){
			PhotoQHelper::debug("POST $key: $value <br />");
			}

		foreach( $_GET as $key => $value){
			PhotoQHelper::debug("GET $key: $value <br />");
			}
		
		PhotoQHelper::debug('leave __construct()');
		*/
	}
	
	


	/**
	 * this is the sink function for the 'admin_menu' hook.
	 * It hooks up the options and management admin panels.
	 */
	function add_admin_pages()
	{
		// Add a new menu under Options:
		$options = add_options_page(__('PhotoQ Options','PhotoQ'), 'PhotoQ', 8, 'whoismanu-photoq.php', array(&$this, 'options_page'));
		// Add a new menu under Manage:
		$manage = add_management_page(__('Manage PhotoQ', 'PhotoQ'), 'PhotoQ', 8, 'whoismanu-photoq.php', array(&$this, 'manage_page'));
		
		//adding javascript and other stuff to header
		
		//have to load it on every page until wordpress gets fixed, otherwise it won't work on translated versions
		add_action('admin_print_scripts-' . $options, array(&$this, 'addCSS'), 1);
		add_action('admin_print_scripts-' . $manage, array(&$this, 'addCSS'), 1);
		add_action('admin_print_scripts-' . $manage, array(&$this, 'addHeaderCode'), 1);
		
	}





	/**
	 * sink function for the 'add_options_page' hook.
	 * displays the page content for the 'PhotoQ Options' submenu
	 */
	function options_page()
	{
		$oldImgDir = $this->_oc->getImgDir();
		/*foreach( $_POST as $key => $value){
			echo "$key: $value <br />";
		}*/
		$this->createDirIfNotExists($this->_oc->getCacheDir(), true);
		
		if (isset($_POST['info_update']) || isset($_POST['addImageSize'])) {
			//check for correct nonce first
			check_admin_referer('photoq-updateOptions');
			
			$this->_oc->update();
			
			
			$statusMsg = __('Options saved.');

			//check whether imgdir changed. if so we have to move all the existing stuff and rebuild the content
			/* //rolled back, coming only in next release
			if($this->_oc->hasChanged('imgDirOption')){
				//$oldImgDir = $this->_oc->getOldValues('imgDirOption');
				//$oldImgDir = $oldImgDir['imgdir']['imgdir'];
				
				$this->moveOldImgDir($oldImgDir);
			}*/
			
			//check whether views or image sizes changed. If so rebuild the thumbs and update the posts accordingly.
			$changedSizes = $this->_oc->getChangedImageSizeNames();
			//if the watermark changed we refresh all images
			if($this->_oc->hasChanged('watermarkOptions'))
				$changedSizes = $this->_oc->getImageSizeNames();
			if(!empty($changedSizes) || $this->_oc->hasChanged(array('contentView','excerptView','exifOptions','originalFolder'))){
				
				$this->rebuildPublished($changedSizes, $this->_oc->hasChanged('exifOptions'),
				!empty($changedSizes) || $this->_oc->hasChanged(array('contentView','excerptView')) ||
				( $this->_oc->hasChanged('exifOptions') && $this->_oc->getValue('inlineExif')),
				$this->_oc->hasChanged('originalFolder'));
				
				if(!empty($changedSizes))
					$statusMsg .= '<br />' . __(' Updated following image sizes: ') . implode(", ", $changedSizes);
				
				$statusMsg .= '<br />' . __(' Updated all published Photos. ');
			}
		
			$saveStatus = new PhotoQStatusMessage($statusMsg);
			$saveStatus->show();

		}
		
		//we are inserting a field into the database
		if (isset($_POST['showWatermarkUploadPanel'])) {
			check_admin_referer('photoq-updateOptions');
			//show watermark upload panel
			require_once(PHOTOQ_PATH.'panels/uploadWatermark.php');
		
		}elseif (isset($_POST['showUpgradePanel'])) {
			check_admin_referer('photoq-updateOptions');
			//show upgrade panel
			require_once(PHOTOQ_PATH.'panels/upgrade.php');
		
		}elseif (isset($_POST['showMoveImgDirPanel'])) {
			check_admin_referer('photoq-updateOptions');
			//show upgrade panel
			require_once(PHOTOQ_PATH.'panels/upgrade-move-imgdir.php');
		
		}else{

		if(isset($_POST['upgradePhotoQ'])){
			check_admin_referer('photoq-updateOptions');
			$status = $this->upgradeFrom12();
		}
		
		elseif(isset($_POST['moveOldImgDir'])){
			check_admin_referer('photoq-updateOptions');
			$status = $this->moveOldImgDir();
		}
		
		elseif(isset($_POST['removeOldYMFolders'])){
			check_admin_referer('photoq-updateOptions');
			foreach($this->getOldYMFolders() as $path)
				PhotoQHelper::recursiveRemoveDir($path);
			
			$status = new PhotoQStatusMessage(__('Cleaned old folder structure.'));
		}
			
		elseif(isset($_POST['uploadWatermark'])){
			check_admin_referer('photoq-updateOptions');
			$status = $this->uploadWatermark();
		}

		//we are inserting a field into the database
		elseif (isset($_POST['addField'])) {
			check_admin_referer('photoq-updateOptions');
			$this->_db->insertField(attribute_escape($_POST['newFieldName']));
		
		} 
		
		//we are renaming a field
		elseif(isset($_POST['rename_field'])){
			//check for correct nonce first
			check_admin_referer('photoq-updateOptions');

			$this->_db->renameField(attribute_escape($_POST['field_id']), attribute_escape($_POST['field_name']));
		}


		//we are deleting a field from the database
		elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
			//check for correct nonce first
			check_admin_referer('photoq-deleteField'.attribute_escape($_GET['entry']));

			$this->_db->removeField( attribute_escape($_GET['entry']) );
		}
			
		elseif (isset($_POST['addImageSize'])) {
			//check for correct nonce first
			check_admin_referer('photoq-updateOptions');
			
			//name has to be save to create directories and not empty.
			$name = preg_replace('/[^a-zA-Z0-9_\-]/','_',$_POST['newImageSizeName']);
			if(!empty($name))
				$status = $this->_oc->addImageSize($name);
			else
				$status = new PhotoQErrorMessage(__('Please choose a valid name for your image size.'));
				
		}
			
		elseif (isset($_GET['action']) && $_GET['action'] == 'deleteImgSize') {
			//check for correct nonce first
			check_admin_referer('photoq-deleteImgSize'.attribute_escape($_GET['entry']));
			$status = $this->_oc->removeImageSize($_GET['entry']);
		}

		//show status of above operations up to here
		if(isset($status)) $status->show();

		
		//do the input validation and show errors if any
		$validationErrors = $this->_oc->validate();

		if(count($validationErrors)){
			$errMsg = '<ul>';
			foreach($validationErrors as $valError){
				$errMsg .= "<li>$valError</li>";
			}
			$errMsg .= '</ul>';
			
			$status =& new PhotoQErrorMessage(__($errMsg));

			$status->show();

		}
		
		//make sure we have freshest data possible.
		$this->_oc->initRuntime();
		
		//show options panel//
		require_once(PHOTOQ_PATH.'panels/options.php');
		}

	}


	/**
	 * sink function for the 'add_management_page' hook.
	 * displays the page content for the 'Manage PhotoQ' submenu
	 */
	function manage_page()
	{
		PhotoQHelper::debug('enter manage_page()');
		
		//do some inital setup
		$this->createDirIfNotExists($this->_oc->getCacheDir(), true);
			
		if ( isset($_POST['add_entry']) || isset($_POST['ftp_upload']) ) {
			//a photo will be added
			
			$this->createDirIfNotExists($this->_oc->getQDir());
			require_once(PHOTOQ_PATH.'panels/upload.php');
			
		}elseif (isset($_POST['edit_batch'])) {
		
			PhotoQHelper::debug('manage_page: load edit-batch panel');
			if(isset($_POST['ftpFiles'])){
				foreach ($_POST['ftpFiles'] as $ftpFile)
					$status = $this->uploadPhoto(basename($ftpFile), '', '', '', $ftpFile);
			}
			require_once(PHOTOQ_PATH.'panels/edit-batch.php');
			PhotoQHelper::debug('manage_page: edit-batch panel loaded');
		
		}elseif (isset($_POST['batch_upload'])) {
			
			if($_POST['batch_upload']){ //check for correct nonce first
				check_admin_referer('photoq-uploadBatch');
			}
			$status = $this->uploadPhoto($_FILES['Filedata']['name'], '', '', '');
			if(!$_POST['batch_upload']){
				$status->show();
				require_once(PHOTOQ_PATH.'panels/edit-batch.php');
			}
		}else{
			if (isset($_POST['save_batch'])) {
					
				PhotoQHelper::debug('manage_page: start saving batch');
	
				//check for correct nonce first
				check_admin_referer('photoq-saveBatch');
	
				//uploaded file info is stored in arrays
				$no_upl = count(attribute_escape($_POST['img_title']));
				
				$qLength = $this->_queue->getLength();
	
				for ($i = 0; $i<$no_upl; $i++) {
					$this->update_queue(attribute_escape($_POST['img_id'][$i]), attribute_escape($_POST['img_title'][$i]), $_POST['img_descr'][$i], attribute_escape($_POST['tags_input'][$i]), attribute_escape($_POST['img_slug'][$i]), attribute_escape($_POST['img_position'][$i]), attribute_escape($_POST['img_position'][$i]), attribute_escape($_POST['img_parent'][$i]), $qLength, $i);
				}
	
				PhotoQHelper::debug('manage_page: batch saved');
					
			}
				
			if (isset($_POST['submit_entry'])) {
				$status = $this->uploadPhoto(attribute_escape($_POST['img_title']), $_POST['img_descr'], attribute_escape($_POST['tags_input']), attribute_escape($_POST['img_slug']));
			}
				
			if (isset($_POST['update_queue'])) {
				//check for correct nonce first
				check_admin_referer('photoq-updateQueue');
				$this->update_queue(attribute_escape($_POST['img_id']), attribute_escape($_POST['img_title']), $_POST['img_descr'], attribute_escape($_POST['tags_input']), attribute_escape($_POST['img_slug']), attribute_escape($_POST['img_position']), attribute_escape($_POST['img_old_position']),attribute_escape($_POST['img_parent'][0]), attribute_escape($_POST['q_length']));
			}
				
			if (isset($_GET['action']) && $_GET['action'] == 'delete') {
				//check for correct nonce first
				check_admin_referer('photoq-deleteQueueEntry' . attribute_escape($_GET['entry']));
				$status = $this->_queue->deletePhotoById(attribute_escape($_GET['entry']));
			}
			
			if (isset($_GET['action']) && $_GET['action'] == 'rebuild') {
				//check for correct nonce first
				$postID = attribute_escape($_GET['id']);
				check_admin_referer('photoq-rebuildPost' . $postID);
				$this->rebuildSinglePhoto($this->_db->getPublishedPhoto($postID),$this->_oc->getImageSizeNames());
				$status =& new PhotoQStatusMessage(__("Photo post with id $postID rebuilt."));
			}
				
			if (isset($_POST['post_first'])) {
				//check for correct nonce first
				check_admin_referer('photoq-manageQueue');
				$status = $this->_queue->publishTop();
			}
			
			if (isset($_POST['post_multi'])) {
				//check for correct nonce first
				check_admin_referer('photoq-manageQueue');
				$status = $this->_queue->publishMulti($this->_oc->getValue('postMulti'));
			}
				
			if (isset($_POST['clear_queue'])) {
				//check for correct nonce first
				check_admin_referer('photoq-manageQueue');
				$this->_queue->deleteAll();
			}
				
			/*get the queue*/
			$this->_queue->load();
			$queue = $this->_queue->getQueue();
			$qLength = $this->_queue->getLength();
				
				
			//show status message if any
			if(isset($status)) $status->show();
				
			/*show the manage panel*/
			require_once(PHOTOQ_PATH.'panels/manage.php');
				
				
		}
		PhotoQHelper::debug('leave manage_page()');
	
	}


	/******************************************************/
	/******************** options page ********************/
	/******************************************************/
	
	/***** functions to handle meta field stuff on options page *****/
	
	/*display the list of currently used metafields*/
	function list_metafields($page, $q_entry = 0)
	{
	
		$results = $this->_db->getAllFields();
	
		if($results){
			$i = 0; //used to alternate styles
			foreach ($results as $field_entry) {
				if($page == 'manage'){ //we are on the manage page
					echo '<div class="info_unit">'.$field_entry->q_field_name.':<br /><textarea style="font-size:small;" name="'.$field_entry->q_field_name.'" cols="30" rows="3"  class="uploadform"></textarea></div>';
				}elseif($page == 'edit_queue'){ //we are editing the queue
					//get posted values if any from common info
					$field_value = $_POST[$field_entry->q_field_name][0];
					if(empty($field_value)){
						//get the stored values
						$field_value = $this->_db->getFieldValue($q_entry, $field_entry->q_field_id);
					}
					echo '<div class="info_unit">'.$field_entry->q_field_name.':<br /><textarea style="font-size:small;" name="'.$field_entry->q_field_name.'[]" cols="30" rows="3"  class="uploadform">'.$field_value.'</textarea></div>';
				}else{ //we are on the options page
					echo '<tr valign="top"';
					if(($i+1)%2) {echo ' class="alternate"';}
					echo '>';
					if ($_GET['action'] == 'rename' && $_GET['entry'] == $field_entry->q_field_id ) {
						echo '<td><p><input type="text" name="field_name" size="15" value="'.$field_entry->q_field_name.'"/></p></td>';
						echo '<td><input type="hidden" name="field_id" size="15" value="'.$field_entry->q_field_id.'"/>&nbsp;</td><td><p><input type="submit" class="button-secondary" name="rename_field" value="Rename field &raquo;" /></p></td>';
					}else{
						echo '<td>'.$field_entry->q_field_name.'</td>';
						echo '<td><a href="options-general.php?page=whoismanu-photoq.php&amp;action=rename&amp;entry='.$field_entry->q_field_id.'" class="edit">Rename</a></td>';
	
						$delete_link = 'options-general.php?page=whoismanu-photoq.php&amp;action=delete&amp;entry='.$field_entry->q_field_id;
						$delete_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($delete_link, 'photoq-deleteField' . $field_entry->q_field_id) : $delete_link;
						echo '<td><a href="'.$delete_link.'" class="delete" onclick="return confirm(\'Are you sure?\');">Delete</a></td>';
					}
					echo '</tr>';
				}
				$i++;
			}
				
		}else{
			echo '<tr valign="top"><td colspan="3">No fields so far. You can add some if you like.</td></tr>';
		}
	
	
	}


	
	/******************************************************/
	/******************** manage page *********************/
	/******************************************************/
	
	
	/***** functions to manage photo queue *****/
	
	//uploads a photo, creates thumbnail and puts it to the end of the queue
	function uploadPhoto($title, $descr, $tags, $slug, $oldPath = '')
	{
		global $wpdb;
	
		PhotoQHelper::debug('enter uploadPhoto()');
	
		//put uploaded file into qdir
		$file = $this->handleUpload($this->_oc->getQDir(), $oldPath);
		
		//check for errors
		if ( isset($file['error']) ) return new PhotoQErrorMessage($file['error']);

		PhotoQHelper::debug('uploadPhoto: upload ok');


		//get return values
		$file = $file['file'];
		$filename = basename($file);
		
		//make nicer titles
		$title = $this->makeAutoTitle($title);
		
		
		//get exif meta data
		$exif = serialize(PhotoQExif::readExif($file));

		//add photo to queue
		$wpdb->query("INSERT INTO $this->QUEUE_TABLE (q_title, q_imgname, q_position, q_slug, q_descr, q_tags, q_exif) VALUES ('$title', '$filename', '".($this->_queue->getLength()+1)."', '$slug', '$descr', '$tags', '$exif')");
		//get the id assigned to this entry
		$imgID = mysql_insert_id();
	
		
		PhotoQHelper::debug('uploadPhoto: post added to DB');
		

		// Insert categories
		$post_categories = apply_filters('category_save_pre', attribute_escape($_POST['post_category']));

		// Check to make sure there is a category, if not just set it to some default
		if (!$post_categories) $post_categories[] = $this->_oc->getValue('qPostDefaultCat');
		foreach ($post_categories as $post_category) {
			// Double check it's not there already
			$exists = $wpdb->get_row("SELECT * FROM $this->QCAT_TABLE WHERE q_fk_img_id = $imgID AND category_id = $post_category");

			if (!$exists) {
				$wpdb->query("
					INSERT INTO $this->QCAT_TABLE
					(q_fk_img_id, category_id)
					VALUES
					($imgID, $post_category)
				");
			}
		}

		//handle the fields
		$results = $wpdb->get_results("SELECT * FROM $this->QFIELDS_TABLE WHERE 1");
		
		$fieldValue = '';
		if($results){
			foreach ($results as $field_entry) {
				//the common info box for ftp uploads submits an array we don't want to use here
				if(!is_array($_POST["$field_entry->q_field_name"]))
					$fieldValue = $_POST["$field_entry->q_field_name"];
				$insert_meta_query = "INSERT INTO $this->QUEUEMETA_TABLE (q_fk_img_id, q_fk_field_id, q_field_value)
					VALUES ($imgID, $field_entry->q_field_id, '".$fieldValue."')";
				$wpdb->query($insert_meta_query);
			}
		}
	
		PhotoQHelper::debug('leave uploadPhoto()');
		
		return new PhotoQStatusMessage(__('Successfully uploaded. \'' . $filename . '\' added to queue at position ' . ($this->_queue->getLength() + 1) . '.', 'PhotoQ'));

	}

		
	//updates a queue entry
	function update_queue($id, $title, $descr, $tags, $slug, $position, $old_position, $parent, $qLength, $pnum = 0)
	{
		global $wpdb;
		PhotoQHelper::debug('enter update_queue()');
	
		if($position < 1)
		$position = 1;
		if($position > $qLength)
		$position = $qLength;
	
		if($position < $old_position){
			$wpdb->query("UPDATE  $this->QUEUE_TABLE SET q_position = q_position+1 WHERE q_position >= '$position' AND q_position < '$old_position'");
		}
		if($position > $old_position){
			$wpdb->query("UPDATE  $this->QUEUE_TABLE SET q_position = q_position-1 WHERE q_position <= '$position' AND q_position > '$old_position'");
		}
	
		$wpdb->query("UPDATE  $this->QUEUE_TABLE SET q_position = '$position', q_title = '$title', q_descr = '$descr', q_tags = '$tags', q_slug = '$slug', q_edited = 1 WHERE q_img_id = $id");
	
		/*update categories*/
		//$q_id = preg_replace('/\./','_',$id); //. in post vars become _
	
		$post_categories = apply_filters('category_save_pre', attribute_escape($_POST['post_category'][$id]));
		// Now it's category time!
		// First the old categories
		$old_categories = $wpdb->get_col("SELECT category_id FROM $this->QCAT_TABLE WHERE q_fk_img_id = $id");
		// Delete any?
		foreach ($old_categories as $old_cat) {
			if (!is_array($post_categories) || !in_array($old_cat, $post_categories)) // If a category was there before but isn't now
			$wpdb->query("DELETE FROM $this->QCAT_TABLE WHERE q_fk_img_id = $id AND category_id = $old_cat LIMIT 1");
		}
	
		// Add any?
		if(is_array($post_categories)){
			foreach ($post_categories as $new_cat) {
				if (!in_array($new_cat, $old_categories))
				$wpdb->query("INSERT INTO $this->QCAT_TABLE (q_fk_img_id, category_id) VALUES ($id, $new_cat)");
			}
		}
		
		//handle the fields
		$results = $wpdb->get_results("
		SELECT
		*
		FROM
		$this->QFIELDS_TABLE
		WHERE 1");
	
		if($results){
			foreach ($results as $field_entry) {
				$update_meta_query = "UPDATE $this->QUEUEMETA_TABLE SET q_field_value = '".$_POST["$field_entry->q_field_name"][$pnum]."'
				WHERE q_fk_img_id = $id && q_fk_field_id = $field_entry->q_field_id";
				$wpdb->query($update_meta_query);
			}
				
		}
	
		PhotoQHelper::debug('leave update_queue()');
	
	}

	
	/** category functions **/
	
	function category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $q_id ) {
		$walker = new Walker_PhotoQ_Category_Checklist($q_id);
		$descendants_and_self = (int) $descendants_and_self;
	
		$args = array();
		
		
		$args['selected_cats'] = array();
		if ( is_array( $selected_cats ) )
			$args['selected_cats'] = $selected_cats;
		$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
		
		$categories = get_categories('get=all');
		
		$args = array($categories, 0, $args);
		$output = call_user_func_array(array(&$walker, 'walk'), $args);
	
		echo $output;
	}
	
	
	function dropdown_categories($q_id = 0, $default = 0) {
		
		global $wpdb;
		
		
		$selectedCats = array();
		//first check for common info
		if ( isset($_POST['post_category']) )
		{
			$selectedCats = $_POST['post_category'][0];
		}
		else if ($q_id) {
			$selectedCats = $wpdb->get_col("
			SELECT category_id
			FROM $this->QCAT_TABLE
			WHERE $this->QCAT_TABLE.q_fk_img_id = $q_id
			 ");
	
			if(count($selectedCats) == 0)
			{
			 	// No selected categories, strange
			 	$selectedCats[] = $default;
			}
	
		} else {
			$selectedCats[] = $default;
		}
		
		$q_id = preg_replace('/\./','_',$q_id); //. in post vars become _
		

		$closed = $this->_oc->getValue('foldCats') ? 'closed' : ''; 
		echo '<div class="postbox '.$closed.'">';
		echo '<h3>Categories</h3>';
		echo '<div class="inside">';
		$this->category_checklist(0,0,$selectedCats,$q_id);
		echo '</div></div>';
	}
	
	/** end category functions **/

	//called by cronjob file
	function cronjob()
	{
		global $wpdb;
	
		PhotoQHelper::debug('enter cronjob()');
	
		//echo "Testing Cron Job";
		//echo "Cron frequency: ".$this->_oc->getValue('cronFreq')." <br />";
		
		
		//calculate time in hours since last post
	
		$currentTime = strtotime(gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * 3600))));
		//echo "Current time: $currentTime <br>";
		//echo 'Current time: '. date('Y-m-d H:i:s', $currentTime) ."<br />";
	
		$lastTime = $wpdb->get_var("SELECT post_date FROM `wp_posts` WHERE post_status = 'publish' ORDER BY post_date DESC");
		if($lastTime){
			//echo "last string: ". $lastTime ."<br />";
			$lastTime = strtotime($lastTime);
		}else{
			PhotoQHelper::debug('cronjob: lastTime was null');
			$lastTime = 0; //somewhere way back in the past, when time started ;-)
		}
	
		//echo "Last post: $lastTime <br />";
		//echo 'Last post: '. date('Y-m-d H:i:s', $lastTime) ."<br />";
	
	
		$timeDifferenceSeconds = $currentTime - $lastTime;
		//echo "seconds = $timeDifferenceSeconds <br />";
	
		$timeDifferenceHours = round($timeDifferenceSeconds / 3600);
		//echo "Diff: $timeDifferenceHours <br />";
	
		if($timeDifferenceHours >= $this->_oc->getValue('cronFreq'))
		$this->_queue->publishTop();
	
		PhotoQHelper::debug('leave cronjob()');
	}
	
	/*sink function executed whenever a post is deleted. Takes post id as argument.
	 Deletes the corresponding image and thumb files from server if post is deleted.*/
	function cleanUp($id)
	{
		//only do this when specific option is set
		if($this->_oc->getValue('deleteImgs')){
			if($this->isPhotoPost($id)){
				$post = get_post($id);
				$photo = new PhotoQPublishedPhoto($post->ID, $post->title);
				$photo->delete();
			}
		}
	}
	
	
	
	/**
	 * Load dbx javascript
	 */
	function addHeaderCode ()
	{
		if (function_exists('wp_enqueue_script')) {
			if($this->_oc->getValue('enableBatchUploads')){
				wp_enqueue_script('swfu-callback', plugins_url('photoq-photoblog-plugin/js/swfu-callback.js'),array('jquery','swfupload'),'20080217');
			}
			
			wp_enqueue_script('ajax-queue', plugins_url('photoq-photoblog-plugin/js/ajax-queue.js'), array('jquery-ui-sortable'),'20080302');
			
		}
	
			
		if($this->_oc->getValue('enableBatchUploads') && ( isset($_POST['add_entry']) || isset($_POST['update_photos']) ) ){
				
			$uploadLink = get_bloginfo('wpurl').'/wp-admin/edit.php?page=whoismanu-photoq.php';
			$uploadLink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($uploadLink, 'photoq-uploadBatch') : $uploadLink;
			//flash doesn't seem to like encoded ampersands, so convert them back here
			$uploadLink = str_replace('&#038;', '&', $uploadLink);
	
			?>
	
	<script type="text/javascript">
	
				var swfu; 
				var uplsize = 0;
				
				
				window.onload = function () { 
					swfu = new SWFUpload({ 
						debug: false,
						upload_url : "<?php echo $uploadLink; ?>", 
						flash_url : "<?php echo includes_url('js/swfupload/swfupload_f9.swf'); ?>", 
						file_size_limit : <?php echo PhotoQHelper::getMaxFileSizeFromPHPINI();?>,	// max allowed by php.ini
						file_queue_limit: 0,
						file_types : "*.jpg;*.gif;*.png",
						file_types_description: "Web Image Files...",
						post_params : { "auth_cookie" : "<?php if ( is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>",
										"batch_upload" : "1",
										"_wpnonce" : "<?php echo wp_create_nonce('photoq-uploadBatch'); ?>" },
						file_queue_error_handler : fileQueueError,
						file_queued_handler : fileQueued, 
						file_dialog_complete_handler : fileDialogComplete, 
						upload_start_handler : uploadStart,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccess,
						upload_complete_handler : uploadComplete
					}); 
					
				};
	
				
			</script>
	
	
			<?php
	} //if($this->_oc->getValue('enableBatchUploads'))
	
	// the following are needed to pass stuff to the ajax js
	?>
	
		<script type="text/javascript">
			var ajaxUrl = "<?php echo plugins_url('photoq-photoblog-plugin/whoismanu-photoq-ajax.php'); ?>";
		</script>
	
	<?php
	
	}
	
	function addCss(){
		?>
	
		<link
		rel="stylesheet"
		href="<?php echo plugins_url('photoq-photoblog-plugin/css/photoq.css');?>"
		type="text/css" />
	
		<?php
		
		wp_enqueue_script('mini-postbox', plugins_url('photoq-photoblog-plugin/js/mini-postbox.js'), array('jquery'),'20080808');
			
		
	}
	
	/**
	 * Checks whether a post is a photo post. A post is considered a photopost if it has a custom
	 * field called photoQPath.
	 *
	 * @param unknown $postID The id of the post to be checked
	 * @return boolean True if the post is photo post
	 * @access public
	 */
	function isPhotoPost($postID)
	{
		$photoQPath = get_post_meta($postID, 'photoQPath', true);
		if(empty($photoQPath)) return false;
		return true;
	}
	
    
	
	/**
	 * This is a filter hooked into the manage_posts_columns WordPress hook. It adds a new column
	 * header for the thumbnail column to the column headers of the manage post list.
	 *
	 * @param string $content	the list of column headers.
	 *
	 * @returns string          the list of column headers including the new column.
	 * @access public
	 */
	function addThumbToListOfPosts($content)
	{
		$result = array();
		foreach( $content as $key => $value){
			$result[$key] = $value;
			//add the new column after the date column
			if($key == "date" && $this->_oc->getValue('showThumbs'))
			$result["photoQPhoto"] = "Photo";
			if($key == "status")
			$result["photoQActions"] = "PhotoQ Actions";
	
		}
		return $result;
	}
	
	
	/**
	 * This is an action hooked into the manage_posts_custom_column WordPress hook. It displays an
	 * additional column in the manage post list containing the thumbnail for photo posts.
	 *
	 * @param string $content     The name of the column to be displayed.
	 * @param string $postID	  The id of the post for which we want to show the photo
	 * @access public
	 */
	function insertThumbIntoListOfPosts($colName, $postID){
		if($colName == "photoQPhoto"){
			if($this->isPhotoPost($postID)){
				$post = get_post($postID);
				echo '<img src="'. 
					$this->getAdminThumbURL(
						get_post_meta($post->ID, 'photoQPath', true), 
						$this->_oc->getValue('showThumbs-Width'), 
						$this->_oc->getValue('showThumbs-Height')
					).'" alt="'.$post->post_title.'" />';
				//$post->post_excerpt;
			}else
				echo "No Photo";
		}
		if($colName == "photoQActions"){
			if($this->isPhotoPost($postID)){
				$rebuildLink = 'edit.php?page=whoismanu-photoq.php&action=rebuild&id='.$postID;
				$rebuildLink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($rebuildLink, 'photoq-rebuildPost' . $postID) : $rebuildLink;
				echo '<a href="'.$rebuildLink.'" title="Rebuild this photo and its post content.">Rebuild</a>';
			}
		}
	}
	
	
	function syncContent($postID, $post)
	{
		PhotoQHelper::debug('enter syncContent()');
		PhotoQHelper::debug('post id: ' . $postID);	
		if($this->isPhotoPost($postID)){
			PhotoQHelper::debug('post id: ' . $postID);	
			$photo = new PhotoQPublishedPhoto($post->ID, $post->post_title);
			$photo->syncContent($post->post_content);
		}
		PhotoQHelper::debug('leave syncContent()');
	}
	
	
	function syncField($data, $postID)
	{
		PhotoQHelper::debug('enter syncField()');
		if($this->isPhotoPost($postID)){
			$post = get_post($postID);
			$photo = new PhotoQPublishedPhoto($post->ID, $post->post_title);
			$data = $photo->syncField($data);	
		}
		PhotoQHelper::debug('leave syncField()');
		return $data;
	}
	
	/**
	 * Generates automatic title form filename. Removes suffix,
	 * replaces underscores by spaces and capitalizes only first
	 * letter of any word.
	 *
	 * @param string $filename
	 * @return string
	 */
	function makeAutoTitle($filename){
		//remove suffix
		$title = preg_replace('/\..*?$/', '', $filename);
		//replace underscores and hyphens with spaces
		$replaceWithWhiteSpace = array('-', '_');
		$title = str_replace($replaceWithWhiteSpace, ' ', $title);
		//proper capitalization
		$title = ucwords(strtolower($title));
		return $title;
	}
	
	/**
	 * Creates directory with path given if it does not yet exist. If an error occurs it
	 * is displayed.
	 *
	 * @param string $dir	The path of the directory to be created.
	 */
	function createDirIfNotExists($dir, $silent=false){
		//create $dir if does not exist yet
		if( !PhotoQHelper::createDir($dir) && !$silent){
			$status =& new PhotoQErrorMessage(__("Error when creating $dir directory. Please check your PhotoQ settings."));
			$status->show();
		}
	}
	
	/**
	 * Moves uploaded file to $destDir. If $oldPath is given by copying from there
	 * (used in case of ftp uploads). Otherwise the wordpress built-in upload handler
	 * is called that copies from the temporary upload directory.
	 *
	 * @param string $destDir
	 * @param string $oldPath
	 * @return array	containing info on uploaded file.
	 */
	function handleUpload($destDir, $oldPath = ''){
		
		if($oldPath === ''){
			/*try to use the functions provided by wordpress, however there is no way to
				specify the upload path other than changing the option, so we do this */
			//save the old value
			$oldUploadPath = get_option('upload_path');
			
			update_option('upload_path', $destDir);
		
			//do the same for yearmonth_folders
			$oldYMFolders = get_option('uploads_use_yearmonth_folders');
			update_option('uploads_use_yearmonth_folders', 0); //turn this off
		
			//set the options that we override
			$overrides = array('action'=>'save');
			$overrides['test_form'] = false; //don't test the form, swfupload is not (yet) able to send additional post vars.
			$overrides['mimes'] = apply_filters('upload_mimes', 
				array (
					'jpg|jpeg|jpe' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'bmp' => 'image/bmp',
					'tif|tiff' => 'image/tiff'
				)
			);
	
			PhotoQHelper::debug('uploadPhoto: start upload');
	
			//upload the thing
			$file = wp_handle_upload($_FILES['Filedata'], $overrides);
			
			//reset upload options to saved ones
			update_option('upload_path', $oldUploadPath);
			update_option('uploads_use_yearmonth_folders',$oldYMFolders);
		}else{ /* ftp upload */
			$newPath = $destDir . basename($oldPath);
			//move file if we have permissions, otherwise copy file
			//suppress warnings if original could not be deleted due to missing permissions
			$ok = @PhotoQHelper::moveFile($oldPath, $newPath);
			if(!$ok) $file['error'] = "Unable to move $oldPath to $newPath";
			$file['file'] = $newPath;	
		}
		return $file;
	}
	
	/**
	 * Handles uploading of a new watermark image.
	 *
	 * @return object	status message.
	 */
	function uploadWatermark(){
		//watermark images can have different suffixes, but we only want one watermark file at a time.
		//instead of finding them all we just delete and recreate the directory.
		$wmDir = $this->_oc->getImgDir().'photoQWatermark/';
		PhotoQHelper::recursiveRemoveDir($wmDir);
		PhotoQHelper::createDir($wmDir);
		//put uploaded file into watermark directory
		$file = $this->handleUpload($wmDir);
		$pathParts = PhotoQHelper::pathInfo($file['file']);
		$newPath = preg_replace("#".$pathParts['filename']."#", 'watermark', $file['file']);
		PhotoQHelper::moveFile($file['file'], $newPath);
		
		if(get_option( "wimpq_watermark" ))
				update_option( "wimpq_watermark", $newPath);
			else
				add_option("wimpq_watermark", $newPath);
		
		$this->rebuildPublished($this->_oc->getImageSizeNames(), false, false, false);
		$statusMsg = __(' New Watermark successfully uploaded. Photos updated.');
		
		$status = new PhotoQStatusMessage($statusMsg);
		return $status;
	}
	
	/**
	 * Display current watermark <img> tag or the string 'None' if there is no watermark.
	 *
	 */
	function showCurrentWatermark(){
		$path = get_option( "wimpq_watermark" );
		if(!$path)
			_e(' None ', 'PhotoQ');
		else{
			$size = getimagesize($path);
			echo '<img class="watermark" width="'.$size[0].'" height="'.$size[1].'" alt="PhotoQ Watermark" src="../'. PhotoQHelper::getRelUrlFromPath($path) .'" />';
		}
	}
	
	/**
	 * Change "original" folder name to a random string if desired.
	 *
	 */
	function updateOriginalFolderName(){
		$newName = 'original';
		if($this->_oc->getValue('hideOriginals')){
			//generate a random name
			$newName .= substr(md5(rand()),0,8);
		}
		$this->_oc->ORIGINAL_IDENTIFIER = $newName;
		
		//update option plus get old name
		$oldName = get_option( "wimpq_originalFolder" );
		if($oldName)
			update_option( "wimpq_originalFolder", $newName);
		else{
			$oldName = 'original';
			add_option("wimpq_originalFolder", $newName);
		}
		
		return array($this->_oc->getImgDir().$oldName, $this->_oc->getImgDir().$newName);
	}
	
	function rebuildPublished($changedSizes, $updateExif, $updateContent, $updateOriginalFolder)
	{
		$publishedPhotos = $this->_db->getAllPublishedPhotos();
		
		$oldNewFolderName = array('','');
		if($updateOriginalFolder){
			$oldNewFolderName = $this->updateOriginalFolderName();
			PhotoQHelper::moveFile($oldNewFolderName[0], $oldNewFolderName[1]);
		}
			
		//remove the image dirs
		foreach ($changedSizes as $changedSize){
			PhotoQHelper::recursiveRemoveDir($this->_oc->getImgDir() . $changedSize . '/');
		}

		//get all photo posts, foreach size, rebuild the photo
		foreach ( $publishedPhotos as $photo ){
			$this->rebuildSinglePhoto($photo, $changedSizes, $updateExif, $updateContent, 
				$updateOriginalFolder, $oldNewFolderName[0], $oldNewFolderName[1]);
		}
	}
	
	function rebuildSinglePhoto($photo, $changedSizes, $updateExif = true, $updateContent = true,
		$updateOriginalFolder = false, $oldFolder = '', $newFolder = ''){
		
			
		if($updateOriginalFolder)
			$photo->updatePath($oldFolder,$newFolder);	
			
		foreach ($changedSizes as $changedSize){
			$photo->rebuildByName($changedSize);
		}
		if(count($changedSizes) || $updateOriginalFolder)
			$photo->updateSizesField();
		
		//update the formatted exif field
		if($updateExif){
			$photo->updateExif();
		}
		//also update the post content like we do for view changes
		if( $updateContent )
			$photo->updatePost();
		
	}
	
	/**
	 * Runs any automatic upgrading things when changing between versions.
	 *
	 */
	function autoUpgrade(){
		if($this->_version != get_option( "wimpq_version" )){
			
			// upgrade to 1.5.2 requires removing content of old photoq cache directory
			// if upgrading from 1.5 ...
			$oldPhotoQPath = str_replace('photoq-photoblog-plugin','whoismanu-photoq',PHOTOQ_PATH);
			$oldCachePath = $oldPhotoQPath . 'cache';
			if(file_exists($oldCachePath)){
				PhotoQHelper::recursiveRemoveDir($oldCachePath);
			}
			
			// ...or removing content of cache directory in other location if upgrading from 1.5.1
			$oldCachePath = PHOTOQ_PATH . 'cache';
			if(file_exists($oldCachePath)){
				PhotoQHelper::recursiveRemoveDir($oldCachePath);
			}
			
			// upgrade to 1.5.2 requires content rebuild because p tags changed to divs
			if($this->_version == '1.5.3' && $this->_oc->getValue('inlineDescr')){
				//get all photo posts, foreach size, rebuild the content
				foreach ( $this->_db->getAllPublishedPhotos() as $photo ){
					@$this->rebuildSinglePhoto($photo, array(), false, true, false, '', '');
				}
			}
			
			// upgrade the database tables
			$this->_db->upgrade($this->_version);
		}	
	}
	
	
	/**
	 * Upgrade pre photoq 1.5 photos to 1.5
	 *
	 */
	function upgradeFrom12(){
		foreach ( $this->_db->getAllPhotos2Import() as $photo ){
			$photo->upgrade();
		}
	}
	
	/**
	 * Something like this will be used to allow users to switch imgdir
	 *
	 */
	/*
	function moveOldImgDir($oldImgDir){
		
		//$publishedPhotos = $this->_db->getAllPublishedPhotos();
		
		//move all files to the new place
		$imgDirContent = $this->getOldImgDirContent( ($oldImgDir) );

		foreach( $imgDirContent as $file2move)
			PhotoQHelper::moveFile($file2move, $this->_oc->getImgDir().basename($file2move));

		//update the watermark directory database entry
		$oldWatermarkPath = get_option( "wimpq_watermark" );
		if($oldWatermarkPath){
			$oldWMFolder = ($oldImgDir).'photoQWatermark/';
			$newWMFolder = $this->_oc->getImgDir().'photoQWatermark/';
			$newWatermarkPath = str_replace($oldWMFolder, $newWMFolder, $oldWatermarkPath);
			update_option( "wimpq_watermark", $newWatermarkPath);
		}
		//get all photo posts, foreach size, rebuild the photo
		foreach ( $this->_db->getAllPublishedPhotos() as $photo ){
			$this->rebuildSinglePhoto($photo, array(), false, true, 
				true, ($oldImgDir).$this->_oc->ORIGINAL_IDENTIFIER, $this->_oc->getImgDir().$this->_oc->ORIGINAL_IDENTIFIER);
		}
		
	}
	*/
	
	/**
	 * Get a list of old (pre photoq 1.5) year-month folders.
	 *
	 * @return array
	 */
	function getOldYMFolders()
	{
		$match = '#^2[0-9]{3}_[01][0-9]$#';
		return PhotoQHelper::getMatchingDirContent($this->_oc->getImgDir(), $match);
	}
	
	/**
	 * Get content of old imgdir for updating to 1.5.2.
	 *
	 * @return array
	 */
	function getOldImgDirContent($oldImgDir)
	{
		//determine which folders we are allowed to move
		$allowedFolders  = array('qdir','photoQWatermark', $this->_oc->getOriginalIdentifier());
		//only thing allowed to be moved are folders related to photoq
		$allowedFolders = array_merge($allowedFolders, $this->_oc->getImageSizeNames());
		for($i = 0; $i<count($allowedFolders); $i++)
			$allowedFolders[$i] = $oldImgDir . $allowedFolders[$i];
		
		//get all visible files from old img dir
		$match = '#^[^\.]#';//exclude hidden files starting with .
		$visibleFiles = PhotoQHelper::getMatchingDirContent($oldImgDir, $match);
		
		//folders that are in both array will be moved
		return array_intersect($allowedFolders, $visibleFiles);
		
	}
	
	/*
	function getOldImgDir($oldImgDir)
	{
		$newImgDir = $this->_oc->getImgDir();
		return str_replace('wp-content', $oldImgDir, $newImgDir);
	}*/
	
	function showFtpFileList(){
		$ftpDir = $this->_oc->getFtpDir();
		echo '<p>Import the following photos from <code>'. $ftpDir . '</code>:</p>';
		if (!is_dir($ftpDir)) {
    		$errMsg = new PhotoQErrorMessage("The directory <code>". $ftpDir . "</code> does not exist on your server.");
			$errMsg->show();
		}else{
			$ftpDirContent = PhotoQHelper::getMatchingDirContent($ftpDir,'#.*\.(jpg|jpeg|png|gif)$#i');
				foreach ($ftpDirContent as $file)
					echo '<input type="checkbox" name="ftpFiles[]" value="'. $file .'" checked="checked" /> '.basename($file).'<br/>';
		
			}
	}

	/**
	 * Shows the edit/enter info form for one photo.
	 *
	 * @param mixed $wimpq_photo	The photo to be edited.
	 */
	function showPhotoInfo($wimpq_photo)
	{
		$img_path = $this->_oc->getQDir() . $wimpq_photo->q_imgname;
		$img_url = "../". PhotoQHelper::getRelUrlFromPath($img_path);
		
		// output photo information form
		$path = $this->getAdminThumbURL($img_path);
		
	?>
		
		<div class="main info_group">
			<div class="info_unit"><a class="img_link" href="<?php echo $img_url; ?>" title="Click to see full-size photo" target="_blank"><img src='<?php echo $path; ?>' alt='<?php echo $wimpq_photo->q_imgname; ?>' /></a></div>
			<div class="info_unit"><label>Title:</label><br /><input type="text" name="img_title[]" size="30" value="<?php echo $wimpq_photo->q_title; ?>" /></div>
			<div class="info_unit"><label>Description:</label><br /><textarea style="font-size:small;" name="img_descr[]" cols="30" rows="3"><?php echo $wimpq_photo->q_descr; ?></textarea></div>
			
			<?php //this makes it retro-compatible
				if(function_exists('get_tags_to_edit')): ?>
			<div class="info_unit"><label><?php _e('Tags (separate multiple tags with commas: cats, pet food, dogs):'); ?></label><br /><input type="text" name="tags_input[]" class="tags-input" id="tags-input" size="50" value="<?php echo $wimpq_photo->q_tags; ?>" /></div>
			<?php endif; ?>
			
			<div class="info_unit"><label>Slug:</label><br /><input type="text" name="img_slug[]" size="12" value="<?php echo $wimpq_photo->q_slug; ?>" /></div>
			<input type="hidden" name="img_id[]" value="<?php echo $wimpq_photo->q_img_id; ?>" />
			<input type="hidden" name="img_position[]" value="<?php echo $wimpq_photo->q_position; ?>" />
		</div>
		<?php
			echo '<div class="info_group">';
			$this->list_metafields('edit_queue',$wimpq_photo->q_img_id);
			echo '</div>';
		?>
		<ul class="wimpq_cats info_group"><?php $this->dropdown_categories($wimpq_photo->q_img_id,$this->_oc->getValue('qPostDefaultCat')); ?></ul>
		<div class="clr"></div>
	<?php
		
	}
	
	function getAdminThumbURL($imgPath, $width = 200, $height = 90)
	{
		$phpThumbLocation = PhotoQHelper::getRelUrlFromPath(PHOTOQ_PATH.'lib/phpThumb_1.7.8/phpThumb.php?');
		$phpThumbParameters = 'src=../../../../../'.PhotoQHelper::getRelUrlFromPath($imgPath).'&amp;h='.$height.'&amp;w='.$width;
		
		$imagemagickPath = 
			( $this->_oc->getValue('imagemagickPath') ? $this->_oc->getValue('imagemagickPath') : null );
		if($imagemagickPath)
			$phpThumbParameters .= '&amp;impath='.$imagemagickPath;
		$imagesrc = $phpThumbLocation.$phpThumbParameters;
		return get_option('siteurl').'/'.$imagesrc;
	}
	
	/**
	 * Hook called upon activation of plugin. 
	 * Installs/Upgrades the database tables.
	 *
	 */
	function activatePlugin()
	{
		PhotoQHelper::debug('enter activatePlugin()');
		$this->_db->upgrade($this->_version);
		PhotoQHelper::debug('leave activatePlugin()');
	}
	
	
	/**
	 * Hook called upon deactivation of plugin.
	 *
	 */
	function deactivatePlugin()
	{
		PhotoQHelper::debug('plugin deactivated');		
	}
	
	/**
	 * Returns the current version of PhotoQ
	 *
	 * @return string	The current version.
	 */
	function getVersion()
	{
		return $this->_version;
	}


}//End Class PhotoQ

/**
 * My own category walker visitor object that will output categories in array syntax such that we can 
 * have multiple category dropdown lists on the same page.
 */
class Walker_PhotoQ_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
	var $q_id;
	/**
	 * PHP4 type constructor
	 */
	function Walker_PhotoQ_Category_Checklist($q_id)
	{
		$this->__construct($q_id);
	}

	/**
	 * PHP5 type constructor
	 */
	function __construct($q_id)
	{
		$this->q_id = $q_id;
	}

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='wimpq_subcats'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='category-$category->term_id-".$this->q_id."'$class>" . '<label for="in-category-' . $category->term_id . '-'.$this->q_id.'" class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="post_category['.$this->q_id.'][]" id="in-category-' . $category->term_id . '-'.$this->q_id.'"' . (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "" ) . '/> ' . wp_specialchars( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
}

?>
