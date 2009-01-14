<div class="wrap">
	<form method="post" action="options-general.php?page=whoismanu-photoq.php"  enctype="multipart/form-data">
		
		
		<h2>PhotoQ Options</h2>
			
			
			<h3><?php _e('Upload New Watermark', 'PhotoQ') ?></h3>
			
			
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
					<th scope="row">
						Locate Watermark:
					</th>
					<td>
						<input type="file" class="button-secondary action" name="Filedata" id="Filedata" />
					</td>
			</tr>
			</table>
			
		
		<?php 
			if ( function_exists('wp_nonce_field') )
					wp_nonce_field('photoq-updateOptions');
		?>
		
		<p class="submit">
			<input type="submit" name="uploadWatermark" class="button-primary"
				value="<?php _e('Upload', 'PhotoQ') ?> &raquo;" />
			<input type="button" id="cancelbtn" class="button-secondary action" onclick="window.location = window.location.href" value="Cancel" />		
			
		</p>
	</form>
</div> 