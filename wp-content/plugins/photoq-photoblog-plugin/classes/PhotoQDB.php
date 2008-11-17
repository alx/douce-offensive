<?php
class PhotoQDB
{
	/**
	 * The wordpress database object to interface with wordpress database
	 * @var Object
	 * @access private
	 */
	var $_wpdb;
	
	/**
	 * ObjectController managing all the plugins options
	 * @var Object
	 * @access private
	 */
	var $_oc;

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
	 * Name of wordpress posts database table
	 * @var string
	 * @access public
	 */
	var $POSTS_TABLE;

	/**
	 * Name of wordpress database table relating posts to custom fields
	 * @var string
	 * @access public
	 */
	var $POSTMETA_TABLE;

	
	/**
	 * PHP4 type constructor
	 *	
	 * @access public
	 */
	function PhotoQDB()
	{
		$this->__construct();
	}
	
	
	/**
	 * PHP5 type constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		global $wpdb;
		
		
		// set wordpress database
		$this->_wpdb =& $wpdb;
		
		// some methods need access to options so instantiate an OptionController
		$this->_oc =& PhotoQSingleton::getInstance('PhotoQOptionController');
		
		// set names of database tables used and created by photoq
		$this->QUEUEMETA_TABLE = $wpdb->prefix."photoqmeta";
		$this->QUEUE_TABLE = $wpdb->prefix."photoq";
		$this->QFIELDS_TABLE = $wpdb->prefix."photoqfields";
		$this->QCAT_TABLE = $wpdb->prefix."photoq2cat";
		
		// set names of wordpress database tables used by photoq
		$this->POSTS_TABLE = $wpdb->prefix."posts";
		$this->POSTMETA_TABLE = $wpdb->prefix."postmeta";
	}
	
	
	/**
	 * Inserts a new custom field into the database.
	 * 
	 * @param string $name		The name of the field to be created.
	 * @access public
	 */
	function insertField($name)
	{
		// TODO: prohibit two fields with same name
		
		//remove whitespace as this will also be used as mysql column header
		$name = preg_replace('/\s+/', '_', $name);
		$this->_wpdb->query("
			INSERT INTO $this->QFIELDS_TABLE (q_field_name) VALUES ('$name')
		");
	
		//get the id assigned to this entry
		$fieldID = mysql_insert_id();
	
		//add also to metatable for all entries in the queue
		$results = $this->_wpdb->get_results("
			SELECT
			q_img_id
			FROM
			$this->QUEUE_TABLE
			WHERE
			1
		");
	
		if($results){
				
			foreach ($results as $queueEntry) {
	
				$insertQuery = "
					INSERT INTO $this->QUEUEMETA_TABLE 
					(q_fk_img_id, q_fk_field_id, q_field_value)
					VALUES ('$queueEntry->q_img_id', $fieldID, '')
				";
				$this->_wpdb->query($insertQuery);
			}
				
		}

		// TODO: this should only be done for photoq posts
		if($this->_oc->getValue('fieldAddPosted')){
			//finally do the same for all posts that have already been posted
			//add also to metatable for all entries in the queue
			$results = $this->_wpdb->get_results("
				SELECT ID FROM $this->POSTS_TABLE WHERE 1
			");
				
			if($results){
				foreach ($results as $postEntry) {			
					$insertQuery = "
						INSERT INTO $this->POSTMETA_TABLE 
						(post_id, meta_key, meta_value)
						VALUES ($postEntry->ID, '$name', ''
					)";
					$this->_wpdb->query($insertQuery);
				}
			}
		}
	}
	
	/**
	 * Remove a custom field from the database.
	 * 
	 * @param int $id		The id of the field to be removed.
	 * @access public
	 */	
	function removeField($id)
	{	
		//get the name before deleting
		$oldName = $this->_wpdb->get_var("SELECT q_field_name FROM $this->QFIELDS_TABLE WHERE q_field_id = $id");
	
		/*delete DB entry*/
		$this->_wpdb->query("DELETE FROM $this->QFIELDS_TABLE WHERE q_field_id = '$id'");
	
		/*delete also from metatable*/
		$this->_wpdb->query("DELETE FROM $this->QUEUEMETA_TABLE WHERE q_fk_field_id = '$id'");
	
		if($this->_oc->getValue('fieldDeletePosted')){
			//delete from already posted posts
			$this->_wpdb->query("DELETE FROM $this->POSTMETA_TABLE WHERE meta_key = '$oldName'");
		}
	
	} 
	
	/**
	 * Rename an exising custom field.
	 * 
	 * @param int $id				The id of the field to be renamed.
	 * @param string $newName		The new name of the field to be renamed.
	 * @access public
	 */
	function renameField($id, $newName)
	{
		// TODO: prohibit two fields with same name

		//get the old name
		$oldName = $this->_wpdb->get_var("SELECT q_field_name FROM $this->QFIELDS_TABLE WHERE q_field_id = $id");
	
		//remove whitespace as this will also be used as mysql column header
		$newName = preg_replace('/\s+/', '_', $newName);
	
		//update DB entry
		$this->_wpdb->query("UPDATE $this->QFIELDS_TABLE SET q_field_name = '$newName' WHERE q_field_id = '$id'");
	
		if($this->_oc->getValue('fieldRenamePosted')){
			//update already posted posts
			$this->_wpdb->query("UPDATE $this->POSTMETA_TABLE SET meta_key = '$newName' WHERE meta_key = '$oldName'");
		}
	
	}
	
	function getAllFields()
	{
		return $this->_wpdb->get_results("
			SELECT * FROM $this->QFIELDS_TABLE
			WHERE 1 ORDER BY q_field_name
		");
	}
	
	/**
	 * As so many other people, we hate the new revision feature of wordpress ;-)
	 * We don't store any revisions of photoQ posts. This function removes all
	 * revisions of post with id $postID.
	 *
	 * @param unknown_type $postID
	 * @return unknown
	 */
	function removeRevisions($postID)
	{
		return $this->_wpdb->get_results("
			DELETE FROM $this->POSTS_TABLE
			WHERE post_type = 'revision' AND post_parent = $postID
		");
		
	}
	
	function getAllPublishedPhotos()
	{
		$photos = array();
		$results = $this->_wpdb->get_results("
			SELECT ID, post_title, meta_value FROM $this->POSTS_TABLE, $this->POSTMETA_TABLE 
			WHERE $this->POSTS_TABLE.ID = $this->POSTMETA_TABLE.post_id AND $this->POSTMETA_TABLE.meta_key = 'photoQPath'");
		foreach ($results as $result){
			$photos[] = new PhotoQPublishedPhoto($result->ID, $result->post_title, '', '', $result->meta_value);
		}
		
		return $photos;
	}
	
	function getPublishedPhoto($postID)
	{
		$result = $this->_wpdb->get_row("
			SELECT post_title, meta_value FROM $this->POSTS_TABLE, $this->POSTMETA_TABLE 
			WHERE $this->POSTS_TABLE.ID = '$postID' AND $this->POSTS_TABLE.ID = $this->POSTMETA_TABLE.post_id AND $this->POSTMETA_TABLE.meta_key = 'photoQPath'");
		return new PhotoQPublishedPhoto($postID, $result->post_title, '', '', $result->meta_value);
	}
	
	/**
	 * Used to import photos from photoq < 2.5
	 *
	 * @return array of photos to import
	 */
	function getAllPhotos2Import()
	{
		$photos = array();
		$results = $this->_wpdb->get_results("
			SELECT ID, post_title, meta_value FROM $this->POSTS_TABLE, $this->POSTMETA_TABLE 
			WHERE $this->POSTS_TABLE.ID = $this->POSTMETA_TABLE.post_id AND $this->POSTMETA_TABLE.meta_key = 'path'");
		foreach ($results as $result){
			$photos[] = new PhotoQImportedPhoto($result->ID, $result->post_title, '', $result->meta_value);
		}
		
		return $photos;
	}
	
		
	function getQueueByPosition()
	{
		return $this->_wpdb->get_results("
		SELECT
		*
		FROM
		$this->QUEUE_TABLE
		WHERE
		1
		ORDER BY q_position
		");
	}
	
	
	// TODO maybe make this a method of a photo object
	function getFieldValue($imgID, $fieldID)
	{
		return $this->_wpdb->get_var("SELECT
				q_field_value
				FROM
				$this->QUEUEMETA_TABLE
				WHERE
				q_fk_img_id = $imgID && q_fk_field_id = $fieldID
		");
	}
	
	// TODO: same here, could be part of a photo object
	function getCategoriesByImgId($id)
	{
		return $this->_wpdb->get_col("SELECT category_id
		FROM $this->QCAT_TABLE
		WHERE q_fk_img_id = $id");
	}
	
	/**
	 * Check whether a certain column exists in a certain table
	 *
	 * @param string $tableName
	 * @param string $colName
	 * @return boolean
	 */
	function colExists($tableName, $colName){
		global $wpdb;
		// Fetch the table column structure from the database
		$colStructures = $wpdb->get_results("DESCRIBE $tableName;");	
		// Check for existence of column $colName
		$colFound = false;
		foreach($colStructures as $colStruct){
			if((strtolower($colStruct->Field) == $colName)){
				$colFound = true;
				break;
			}
		}
		return $colFound;
	}
	
	/**
	 * Check whether a certain database table exists.
	 *
	 * @param string $tableName
	 * @return boolean
	 */
	function tableExists($tableName){
		global $wpdb;
		$tables = $wpdb->get_col("SHOW TABLES;");
		$tableFound = false;
		foreach($tables as $table){
			if(strtolower($table) == $tableName){
				$tableFound = true;
				break;
			}
		}
		return $tableFound;
	}
	
	
	/**
	 * Upgrades/Installs database tables. Contains the table definitions.
	 *
	 * @param string $currentVersion
	 * @access public
	 */
	function upgrade($currentVersion)
	{
		global $wpdb;
		//these steps are required to upgrade to 1.5.2 with the new q_img_id key
		//because the standard wordpress delta does not do the job correctly.
		
		//add the new key to the queue table
		if($this->tableExists($this->QUEUE_TABLE) && !$this->colExists($this->QUEUE_TABLE, 'q_img_id')){
			$wpdb->query("ALTER TABLE $this->QUEUE_TABLE DROP PRIMARY KEY");
			$wpdb->query("ALTER TABLE $this->QUEUE_TABLE ADD COLUMN q_img_id bigint(20) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY  (q_img_id)");	
		}
		
		//add new column for q_fk_img_id to queue meta table and match it with queue table
		if($this->tableExists($this->QUEUEMETA_TABLE) && !$this->colExists($this->QUEUEMETA_TABLE, 'q_fk_img_id')){
			//add column
			$wpdb->query("ALTER TABLE $this->QUEUEMETA_TABLE ADD COLUMN q_fk_img_id bigint(20) NOT NULL default '0'");	
		
			//populate the new ids with correct values
			$results = $wpdb->get_results("
				SELECT
				*
				FROM
				$this->QUEUE_TABLE
				WHERE 1"
			);
			if($results){
				foreach ($results as $img) {
					$update_meta_query = "UPDATE $this->QUEUEMETA_TABLE SET q_fk_img_id = $img->q_img_id
					WHERE q_fk_imgname = '".$img->q_imgname."'";
					$wpdb->query($update_meta_query);
				}
					
			}
			
			//delete the q_fk_imgname column and set the new primary key
			$wpdb->query("ALTER TABLE $this->QUEUEMETA_TABLE DROP PRIMARY KEY");
			$wpdb->query("ALTER TABLE $this->QUEUEMETA_TABLE ADD PRIMARY KEY  (q_fk_img_id,q_fk_field_id)");
			$wpdb->query("ALTER TABLE $this->QUEUEMETA_TABLE DROP q_fk_imgname");
		
		}
		
		
		//do the same for the cat table
		//add new column for q_fk_img_id to cat table and match it with queue table
		if($this->tableExists($this->QCAT_TABLE) && !$this->colExists($this->QCAT_TABLE, 'q_fk_img_id')){
			//add column
			$wpdb->query("ALTER TABLE $this->QCAT_TABLE ADD COLUMN q_fk_img_id bigint(20) NOT NULL default '0'");	
		
			//populate the new ids with correct values
			$results = $wpdb->get_results("
				SELECT
				*
				FROM
				$this->QUEUE_TABLE
				WHERE 1"
			);
			if($results){
				foreach ($results as $img) {
					$update_meta_query = "UPDATE $this->QCAT_TABLE SET q_fk_img_id = $img->q_img_id
					WHERE q_imgname = '".$img->q_imgname."'";
					$wpdb->query($update_meta_query);
				}
					
			}
			
			//delete the q_imgname column
			$wpdb->query("ALTER TABLE $this->QCAT_TABLE DROP q_imgname");
		
		}
		
		//determine charset/collation stuff same way wordpress does
		if ( $wpdb->supports_collation() ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		
		//echo "creating Table: ".$this->QUEUE_TABLE;
	
		$sql = "
		CREATE TABLE $this->QUEUE_TABLE (
		q_img_id bigint(20) NOT NULL AUTO_INCREMENT,
		q_position int(10) NOT NULL default '0',
		q_title varchar(200) default '',
		q_imgname varchar(200) NOT NULL default '',
		q_slug varchar(200) default '',
		q_descr text default '',
		q_tags text default '',
		q_exif text default '',
		q_edited bit default 0,
		PRIMARY KEY  (q_img_id)
		) $charset_collate;";
		$this->_upgradeDB($this->QUEUE_TABLE, $sql, $currentVersion);
	
		//echo "<br>creating Table: ".$this->QCAT_TABLE;
	
		$sql = "
		CREATE TABLE $this->QCAT_TABLE (
		rel_id bigint(20) NOT NULL AUTO_INCREMENT,
		category_id bigint(20) NOT NULL default '0',
		q_fk_img_id bigint(20) NOT NULL default '0',
		PRIMARY KEY  (rel_id)
		) $charset_collate;";
		$this->_upgradeDB($this->QCAT_TABLE, $sql, $currentVersion);
		
		//echo "<br>creating Table: ".$this->QFIELDS_TABLE;
	
		$sql = "
		CREATE TABLE $this->QFIELDS_TABLE (
		q_field_id bigint(20) NOT NULL AUTO_INCREMENT,
		q_field_name varchar(200) NOT NULL default '',
		PRIMARY KEY  (q_field_id)
		) $charset_collate;";
		$this->_upgradeDB($this->QFIELDS_TABLE, $sql, $currentVersion);
	
	
		//echo "<br>creating Table: ".$this->QUEUEMETA_TABLE;
	
		$sql = "
		CREATE TABLE $this->QUEUEMETA_TABLE (
		q_fk_img_id bigint(20) NOT NULL default '0',
		q_fk_field_id bigint(20) NOT NULL default '0',
		q_field_value text,
		PRIMARY KEY  (q_fk_img_id,q_fk_field_id)
		) $charset_collate;";
		$this->_upgradeDB($this->QUEUEMETA_TABLE, $sql, $currentVersion);
		
	}
	
	/**
	 * Upgrades the Wordpress Database Table. 
	 * Done according to the instructions given here: 
	 * 
	 * http://codex.wordpress.org/Creating_Tables_with_Plugins
	 *
	 * @param string $table	The name of the table to update.
	 * @param string $sql	The query to run.
	 * @param string $currentVersion The current PhotoQ version.
	 * @access private
	 */
	function _upgradeDB($table, $sql, $currentVersion) {
		if($this->_wpdb->get_var("show tables like '$table'") != $table 
					|| $currentVersion != get_option( "wimpq_version" )) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			if(get_option( "wimpq_version" )){
				update_option( "wimpq_version", $currentVersion);
			}else
				add_option("wimpq_version", $currentVersion);
		}
	}
	
	function deleteQueueEntry($id, $position){

		//delete DB entry
		$this->_wpdb->query("DELETE FROM $this->QUEUE_TABLE WHERE q_img_id = $id");
		//delete cat entries
		$this->_wpdb->query("DELETE FROM $this->QCAT_TABLE WHERE q_fk_img_id = $id");
		//delete field entries
		$this->_wpdb->query("DELETE FROM $this->QUEUEMETA_TABLE WHERE q_fk_img_id = $id");
		//update queue positions
		$this->_wpdb->query("UPDATE  $this->QUEUE_TABLE SET q_position = q_position-1 WHERE q_position > '$position'");

	}

	
}
?>
