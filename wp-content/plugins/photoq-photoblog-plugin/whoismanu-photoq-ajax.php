<?php

// first lines to include all the stuff from admin-ajax.php 
define('DOING_AJAX', true);

require_once('../../../wp-load.php');
//require_once('../../../wp-config.php');
require_once('../../../wp-admin/includes/admin.php');


if ( !is_user_logged_in() )
	die('-1');

// end stuff from admin-ajax.php

PhotoQHelper::debug('got ajax call');

foreach( $_POST as $key => $value)
	PhotoQHelper::debug("POST $key: $value");


switch ( $_POST['action'] ) :
case 'reorder' :
	check_ajax_referer( 'queueReorder', 'queueReorderNonce' );
	
	PhotoQHelper::debug('reordering queue');
	PhotoQHelper::debug(sizeof($_POST['photoq']));
	//get the current queue and check that both arrays have same size
	$queue = $photoq->_queue->getQueue();
	$q_length = sizeof($queue);
	sizeof($_POST['photoq']) == $q_length or die('1');
		
	PhotoQHelper::debug('sanity check passed');
	
	global $wpdb;
	for($i=0; $i<$q_length; $i++){
		if($_POST['photoq'][$i] != $queue[$i]->q_img_id){			
			$wpdb->query("UPDATE $photoq->QUEUE_TABLE SET q_position = '".($i+1)."' WHERE q_img_id = '" . attribute_escape($_POST['photoq'][$i]) . "'");
		}
	}
		
	break;


	
case 'edit' :
	$wimpq_photo = $wpdb->get_row("SELECT * FROM $photoq->QUEUE_TABLE WHERE q_img_id = '".attribute_escape($_POST['id'])."'");	
	PhotoQHelper::debug('starting ajax editing');
		
	?>
		<form method="post" enctype="multipart/form-data" action="edit.php?page=whoismanu-photoq.php">	
			<div class="photo_info">
		
	<?php 
	PhotoQHelper::debug('started form');
		if ( function_exists('wp_nonce_field') )
			wp_nonce_field('photoq-saveBatch');
	PhotoQHelper::debug('passed nonce');				
		$photoq->showPhotoInfo($wimpq_photo);		
	PhotoQHelper::debug('showed photo');	
	?>
		
				<div class="submit">
					<input type="submit" class="button-primary submit-btn" name="save_batch" value="<?php _e('Save Changes', 'PhotoQ') ?>" />
					<input type="submit" class="button-secondary submit-btn" onClick="window.location = window.location.href;" 
					value="<?php _e('Cancel', 'PhotoQ') ?>" />
				</div>
			</div>
		</form>
	
	<?php
	PhotoQHelper::debug('form over');
	
endswitch;

?>