<?php 
	PhotoQHelper::debug('manage_page: reached edit-batch panel');
?>

<div class="wrap">
	<h2>Manage PhotoQ - Enter Info</h2>	
<form method="post" enctype="multipart/form-data" action="edit.php?page=whoismanu-photoq.php">	
	
<div id="poststuff">
<?php 
	
	global $wpdb;
	
	if ( function_exists('wp_nonce_field') )
		wp_nonce_field('photoq-saveBatch');

		
	$wimpq_photos = array();
	
	$results = $wpdb->get_results("
		SELECT 
			*
		FROM 
			$this->QUEUE_TABLE
		WHERE 
			q_edited = 0
		ORDER BY q_position
		");
	$wpdb->show_errors();
	if($results){
		$count = 0;
		foreach ($results as $q_entry) {
			$wimpq_photos[$count]->q_img_id = $q_entry->q_img_id;
			$wimpq_photos[$count]->q_imgname = $q_entry->q_imgname;
			$wimpq_photos[$count]->q_position = $q_entry->q_position;
			$wimpq_photos[$count]->q_title = $q_entry->q_title;
			$wimpq_photos[$count]->q_slug = $q_entry->q_slug;
			//if we have post values (common info) we take those instead of db value.
			if($_POST['img_descr'])
				$wimpq_photos[$count]->q_descr = $_POST['img_descr'];
			else
				$wimpq_photos[$count]->q_descr = $q_entry->q_descr;
				
			if($_POST['tags_input'])
				$wimpq_photos[$count]->q_tags = $_POST['tags_input'];
			else
				$wimpq_photos[$count]->q_tags = $q_entry->q_tags;	
			$count++;
		}
	}
	
	foreach ($wimpq_photos as $wimpq_photo){
		echo '<div class="photo_info">';
		$this->showPhotoInfo($wimpq_photo);
		echo '</div>';
	}
	
?>
	</div>
		<div>
			<input type="submit" class="button-primary action" name="save_batch" 
			value="<?php _e('Save Batch Info', 'PhotoQ') ?> &raquo;" />
		</div>
	</form>		
</div> 