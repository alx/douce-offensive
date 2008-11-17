<div class="wrap">
	<form method="post" action="options-general.php?page=whoismanu-photoq.php">
		
		<h2>PhotoQ Options</h2>
		
		<p class="submit top-savebtn">
			<input type="submit" name="info_update" 
				value="<?php _e('Save Changes', 'PhotoQ') ?> &raquo;" />
		</p>
			
		
			
			<div>
			&nbsp;
			</div>
				
			<div id="poststuff">
			
			
			<div  class="postbox ">
			<h3><?php _e('Image sizes', 'PhotoQ') ?></h3>
			<div class="inside">
			
			
			<?php $this->_oc->render('imageSizes');?>
			
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
					<th scope="row">
						<label for="newImageSizeName">Name of new image size: </label>
					</th>
					<td>
			<input type="text" name="newImageSizeName" id="newImageSizeName"
					size="20" maxlength="20" value="" />
			<input type="submit" class="button-secondary"
					name="addImageSize"
					value="<?php _e('Add Image Size', 'PhotoQ') ?> &raquo;" />
			
					</td>
			</tr>
			</table>
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
				<th scope="row"><?php _e('Hide \'original\' folder:', 'PhotoQ'); ?></th>
				<td>
				<?php 
					$this->_oc->render('originalFolder');
					
					$folderName = get_option('wimpq_originalFolder');
					$folderName = $folderName ? $folderName : 'original';
					echo '<br/>(Current name: '.$folderName.')';
				?></td>
			</tr>
			</table>
			</div>
			</div>
			
			<div  class="postbox ">
			<h3><?php _e('Views', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th>the_content</th>
					<td></td>
				</tr>
				<?php $this->_oc->render('contentView');?>
			</table>	
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th>the_excerpt</th>
					<td></td>
				</tr>
				<?php $this->_oc->render('excerptView');?>
			</table>
			</div>
			</div>
			
			
			<div  class="postbox closed">
			<h3><?php _e('Exif', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Choose Exif Tags:', 'PhotoQ'); ?></th>
					<td>
						<?php 
							if(!get_option( "wimpq_exif_tags" )) 
								_e('Not tags yet. PhotoQ will learn exif tags from uploaded photos. Upload a photo first,
								then come back and choose your exif tags here.', 'PhotoQ');
						?>
						<ul class="exifTags">
							<?php $this->_oc->render('exifOptions');?>
						</ul>
					</td>
				</tr>
			</table>
			</div>
			</div>
			
			
			<div  class="postbox closed">
			<h3><?php _e('Watermarking', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
			<tr valign="top">
					<th scope="row"><?php _e('Watermark Image:', 'PhotoQ') ?></th>
					<td>
					<?php $this->showCurrentWatermark(); ?>
			
			<input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showWatermarkUploadPanel"
					value="<?php _e('Change Watermark', 'PhotoQ') ?> &raquo;" />
					</td>
			</tr>
			
			<?php $this->_oc->render('watermarkOptions');?>
			
			</table>
			</div>
			</div>
			
			
			
			<div  class="postbox closed">
			<h3><?php _e('Meta Fields', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				<tr valign="top">
					<th scope="row">
						<?php _e('Upon Add:', 'PhotoQ'); ?>
					</th>
					<td>
						<?php $this->_oc->render('fieldAddPosted');?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e('Upon Delete:', 'PhotoQ'); ?>
					</th>
					<td>
						<?php $this->_oc->render('fieldDeletePosted');?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e('Upon Rename:', 'PhotoQ'); ?>
					</th>
					<td>
						<?php $this->_oc->render('fieldRenamePosted');?>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e('Defined Fields:', 'PhotoQ'); ?></th>
					<td>
						<table width="200" cellspacing="2" cellpadding="5"
							class="meta_fields noborder">

							<?php
								$this->list_metafields('options');				
							?>
				
						</table>
					</td>
				</tr>	
			</table>
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
				<tr valign="top">
					<th scope="row">
						<label for="newFieldName">Name of new field: </label>
					</th>
					<td>
						<input type="text" name="newFieldName" id="newFieldName"
								size="20" maxlength="20" value="" />
						<input type="submit" class="button-secondary"
								name="addField"
								value="<?php _e('Add Meta Field', 'PhotoQ') ?> &raquo;" />
					</td>
				</tr>
			</table>
			</div>
			</div>
		
			<div  class="postbox closed">
			<h3><?php _e('Further Options', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				
				<?php 
				
				$furtherOptions = array(
					'imgdir' => __('Image Directory:', 'PhotoQ'),
					'imagemagickPath' => __('ImageMagick Path:', 'PhotoQ'),
					'cronFreq' => __('Cron Job Frequency:', 'PhotoQ'),
					'qPostAuthor' => __('PhotoQ Post Author:', 'PhotoQ'),
					'qPostDefaultCat' => __('PhotoQ Default Category:', 'PhotoQ'),
					'foldCats' => __('Fold Categories:', 'PhotoQ'),
					'showThumbs' => __('Managing Posts:', 'PhotoQ'),
					'enableFtpUploads' => __('FTP Upload:', 'PhotoQ'),
					'postMulti' => __('Second Post Button:', 'PhotoQ'),
					'deleteImgs' => __('Deleting Posts:', 'PhotoQ'),
					'enableBatchUploads' => __('Batch Uploads:', 'PhotoQ')
				); 
				
				
				foreach ($furtherOptions as $optName => $optLabel){
					echo '<tr valign="top">';
					echo '<th scope="row">'.$optLabel.'</th><td>';
					$this->_oc->render($optName);
					echo '</td></tr>';
				}
				
				
				?>
				
			</table>
			</div>
			</div>
			
			<div  class="postbox closed">
			<h3><?php _e('Upgrading', 'PhotoQ') ?></h3>
			<div class="inside">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
				
				<?php if(false): //eventually move this out completely?>
				<tr valign="top">
					<th scope="row"><?php _e('PhotoQ < 1.5.2:','PhotoQ') ?></th>
					<td><label for="oldImgDir">Old Image Directory: </label>
					<input type="text" name="oldImgDir" id="oldImgDir"
					size="20" maxlength="20" value="" />
					<input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showMoveImgDirPanel"
					value="<?php _e('Move ImgDir to wp-content', 'PhotoQ') ?> &raquo;" /></td>
				</tr>
				<?php endif; ?>
				
				<tr valign="top">
					<th scope="row"><?php _e('PhotoQ < 1.5:','PhotoQ') ?></th>
					<td><input style="vertical-align: top;" type="submit" class="button-secondary"
					name="showUpgradePanel"
					value="<?php _e('Upgrade from PhotoQ < 1.5', 'PhotoQ') ?> &raquo;" /></td>
				</tr>
				
			</table>
			</div>
			</div>
		
		
		<?php 
			if ( function_exists('wp_nonce_field') )
					wp_nonce_field('photoq-updateOptions');
		?>
		
		<p class="submit">
			<input type="submit" name="info_update" 
				value="<?php _e('Save Changes', 'PhotoQ') ?> &raquo;" />
		</p>
		</div>
	</form>
</div> 