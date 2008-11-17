<div class="wrap">


<form method="post" action="edit.php?page=whoismanu-photoq.php">
<h2>Manage PhotoQ</h2>
<div id="poststuff">

<?php 

wp_nonce_field( 'queueReorder', 'queueReorderNonce', false ); 


?> 



<div class="qlen">		
	<?php 
	if($queue)
		echo 'Queue length: '.$qLength;
	else
		echo "Queue empty, why not upload some photos?";
	?>
</div>


<div class="tablenav">
	
	
	<input type="submit" style="float: right; margin-left: 5px" class="button-secondary" name="post_first"
		value="<?php _e('Post Top of Queue...', 'PhotoQ') ?>"
		onClick="return confirm(
	'Are you sure you want to publish the first entry of the queue?');" />
	
	<?php 
		$num2Post = $this->_oc->getValue('postMulti');
		if(is_numeric($num2Post) && $num2Post > 1):
			$btnString = "Post Next $num2Post Photos...";
			if($num2Post >= $qLength)
				$btnString = 'Post Whole Queue...';
	?>
	<input type="submit" style="float: right" class="button-secondary" name="post_multi"
		value="<?php echo $btnString; ?>"
		onClick="return confirm(
	'Are you really sure you want to publish the next entries in the queue?');" />
	<?php endif;?>
	
	
	<input type="submit" class="button-secondary" name="add_entry"
		value="<?php _e('Add Photos to Queue', 'PhotoQ') ?>" />

	<input type="submit" class="button-secondary" name="clear_queue"
		value="<?php _e('Clear Queue...', 'PhotoQ') ?>"
		onClick="return confirm(
	'Are you sure you want to clear the entire queue?');" />
	
</div>
<div class="clr"></div>
<?php if($queue){ ?>

<div id="qHeader" class="thead">
	<div class="qHCol qHPosition">Position</div>
		<div class="qHCol qThumb">Thumbnail</div>
		<div class="qHCol qTitle">Title</div>
		<div class="qHCol qDescr">Description</div>
		<div class="qHCol qEdit"></div>
		<div class="qHCol qDelete"></div>
		<div class="clr">&nbsp;</div>
</div>


	<ul id="photoq">

<?php

	for ($i = 0; $i < $qLength; $i++){
		//get the name of the thumbnail
		$img_path = $this->_oc->getQDir() . $queue[$i]->q_imgname;
		$img_url = "../". PhotoQHelper::getRelUrlFromPath($img_path);

		$path = $this->getAdminThumbURL($this->_oc->getQDir() . $queue[$i]->q_imgname);
		
?>

		<li id="photoq-<?php echo $queue[$i]->q_img_id; ?>" class='photoqEntry'>
			<div class="qCol qPosition"><?php echo $queue[$i]->q_position; ?></div>
			<div class="qCol qThumb">
				<a class="img_link" href="<?php echo $img_url; ?>" title="Click to see full-size photo" target="_blank">
					<img src='<?php echo $path; ?>' alt='<?php echo $queue[$i]->q_title; ?>' />
				</a>
			</div>
			<div class="qCol qTitle"><?php echo $queue[$i]->q_title; ?></div>
			<div class="qCol qDescr"><?php if($queue[$i]->q_descr) echo $queue[$i]->q_descr; else echo "&nbsp;"; ?></div>

<?php
		$delete_link = 'edit.php?page=whoismanu-photoq.php&action=delete&entry='.$queue[$i]->q_img_id;
		$delete_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($delete_link, 'photoq-deleteQueueEntry' . $queue[$i]->q_img_id) : $delete_link;
		
?>
		<div class="qCol qEdit">
			<a href="" onClick="return editQEntry('<?php echo $queue[$i]->q_img_id; ?>');">Edit</a>
		</div>
		<div class="qCol qDelete">
			<a href="<?php echo $delete_link; ?>" onClick="return confirm('Delete entry? Correpsonding image will also be deleted from server?');">Delete</a>
		</div>
		<div class="clr">&nbsp;</div>

		</li>
<?php 	
	}

	echo "</ul>";

}//if(queue)

if ( function_exists('wp_nonce_field') ){
	wp_nonce_field('photoq-manageQueue');
}
?>

</div>

</form>




</div>
