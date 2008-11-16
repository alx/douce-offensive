
<?php if (!empty($this->message)) : ?>
	<div id="message" class="updated fade"><p><?php echo $this->message ?></p></div>
<?php endif; ?>

<!-- YAPB Admin Panel Styles -->
<link rel="stylesheet" type="text/css" href="<?php echo YAPB_PLUGIN_PATH ?>tpl/admin_panel_yapb_options_page.tpl.css" />

<!-- jQuery UI Accordion -->
<script type="text/javascript" src="<?php echo YAPB_PLUGIN_PATH ?>tpl/js/jquery.scrollTo-min.js"></script>
<script type="text/javascript" src="<?php echo YAPB_PLUGIN_PATH ?>tpl/js/jquery.dimensions.js"></script>
<script type="text/javascript" src="<?php echo YAPB_PLUGIN_PATH ?>tpl/js/jquery.ui-1.5b/ui.accordion.js"></script>
<script type="text/javascript">
	//<![CDATA[
	
	(function($) {

		$(document).ready(
			function() {

				$("#accordion")
				.accordion(
					{
						autoheight:false,
						header:".basic-accordion-link",
						active:<?php echo $this->active ?>,
						alwaysOpen:false
					}
				);

				$(".ui-accordion").bind("change.ui-accordion", function(event, ui) {
					
					var instance = $.data(this, "ui-accordion");
					var options = instance.options;
					var headers = $(options.headers);

					// Find 

					var foundIndex = 'false';
					for (i=0; i<headers.length; i++) {
						if ($(headers[i]).html() == ui.newHeader.html()) {
							foundIndex = i;
							break;
						}
					}

					// we submit the foundindex+1 so the php backend doesn't
					// interpret 0 as false
					$('#accordionindex').attr('value', foundIndex+1);

					// scroll to the start of the tabs
					$.scrollTo($(".basic-accordion-anchor"), 500);

				});

				<?php if (Params::get('accordionindex', null) != null): ?>

					// If we sent an update with an open accordion,
					// we scroll to the accordion anchor

					$.scrollTo($(".basic-accordion-anchor"), 500);

				<?php endif ?>

			}
		)

	})(jQuery);

	//]]>
</script>

<!-- /jQuery Accordion -->

<div class="wrap">

	<?php global $yapb ?>

	<h2><?php printf(__('Yet Another Photoblog %s', 'yapb'), $this->yapbVersion) ?></h2>

	<table class="form-table">
		<tr valign="top" class="yapb-stat">
			<th scope="row">Your Photoblog</th>
			<td>
				<?php
					
					// Since i'm not able to cleanly remove all division by zero
					// bugs in this script, i'm removing all divisions with this function ;-)
					function saveDiv($param1, $param2) {
						$result = 0;
						if ($param2 != 0) {
							$result = $param1 / $param2;
						}
						return $result;
					}

					$imagefileCount = $this->maintainance->getImagefileCount();
					$imagefileSize = $this->maintainance->getImagefileSizeBytes();
					$cachefileCount = $this->maintainance->getCachefileCount();
					$cachefileSize = $this->maintainance->getCachefileSizeBytes(); 

					$averageImagefileSize = round(saveDiv($imagefileSize, $imagefileCount), 2);

					function strong($text) {
						return '<strong>' . $text . '</strong>';
					}
					
					function sizePresentation($sizeInBytes) {
						if ($sizeInBytes < 1048576) {
							return strong(round($sizeInBytes / 1024, 0)) . ' KB';
						} else {
							return strong(round($sizeInBytes / 1024 / 1024, 1)) . ' MB';
						}
					}



				?>

				<table cellspacing="0" cellpadding="0" border="0" style="margin:0;padding:0;border:0;">
					<tr>
						<td valign="top" style="margin:0;padding:0 10px 0 0;border:0;">

							<span class="big">
								<strong><?php echo $imagefileCount ?></strong> <?php _e('Images', 'yapb') ?>
							</span>

							<ul class="yapb">
								<li><?php printf(__('You have posted %s YAPB-Images with an overall size of %s.', 'yapb'), strong($imagefileCount), sizePresentation($imagefileSize)) ?></li>
								<?php if ($imagefileCount > 0): ?><li><?php printf(__('In average, an image needs %s of disk space.', 'yapb'), sizePresentation(saveDiv($imagefileSize, $imagefileCount))) ?></li><?php endif ?>
							</ul>

						</td>
						<td valign="top" style="margin:0;padding:0 10px 0 10px;border:0;">

							<span class="big">
								<strong><?php echo $cachefileCount ?></strong> <?php _e('Thumbnails','yapb') ?><br />
							</span>
									
							<?php if ($cachefileCount > 0): ?>
								<ul class="yapb">
									<li><?php printf(__('Currently, the cache contains %s thumbnails with a overall size of %s.', 'yapb'), $cachefileCount, sizePresentation($cachefileSize)) ?></li>
									<li><?php printf(__('In average, a thumbnail needs %s of disk space.', 'yapb'), sizePresentation(saveDiv($cachefileSize, $cachefileCount))) ?></li>
									<li>
										<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="yapb-clear-cache">
											<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"> 
											<input type="hidden" name="action" value="clear_cache" /> 
											<input type="submit" name="clear" value="<?php _e('Empty thumbnail cache','yapb') ?>" />
										</form>
									</li>
								</ul>
							<?php else: ?>
								<ul class="yapb">
									<li>no thumbnails were generated yet.</li>
								</ul>
							<?php endif ?>

						</td>
						<td valign="top" style="margin:0;padding:0 0 0 10px;border:0;">

							<span class="big">
								<?php echo sizePresentation($imagefileSize+$cachefileSize) ?>
							</span>

							<ul class="yapb">
								<li><?php printf(__('Currently, YAPB consumes %s of disk space for images.', 'yapb'), sizePresentation($imagefileSize + $cachefileSize)) ?></li>
								<?php if ($cachefileCount > 0): ?>
									<li><?php printf(__('In average, %s thumbnails per image were generated.', 'yapb'), strong(round(saveDiv($cachefileCount, $imagefileCount), 2))) ?></li>
									<li><?php printf(__('In average, one posted image incl. all associated thumbnails needs approx. %s of disk space.', 'yapb'), sizePresentation(saveDiv($cachefileCount, $imagefileCount) * saveDiv($cachefileSize, $cachefileCount) + saveDiv($imagefileSize, $imagefileCount))) ?></li>
								<?php endif; ?>
							</ul>

						</td>
					</tr>
				</table>

			</td>
		</tr>

		<tr class="yapb-support">
			<th scope="row">Support</th>
			<td valign="top">

				<table border="0" cellspacing="0" cellpadding="0" class="yapb-transparent">
					<tr>
						
						<td valign="top" style="margin:0;padding:0 10px 0 0;border:0;">

							<span class="big"><?php echo __('YAPB Support', 'yapb') ?></span>
							<p><?php echo __('There\'s a growing community using YAPB to publish their photos via WordPress - Don\'t hesitate to ask for help or share your knowledge in the YAPB-Forum:', 'yapb') ?></p>
							<p><a href="http://johannes.jarolim.com/yapb-forum" target="_blank">http://johannes.jarolim.com/yapb-forum</a></p>

							<span class="big" style="margin-top:20px;"><?php echo __('YAPB Plugins', 'yapb') ?></span>
							<p><?php echo __('Extend your photoblog by adding YAPB Plugins. A list of all extension like the YAPB Sidebar Widget may be found here:', 'yapb') ?></p>
							<p><a href="http://johannes.jarolim.com/yapb/plugins" target="_blank">http://johannes.jarolim.com/yapb/plugins</a></p>

						</td>

						<td valign="top" style="margin:0;padding:0 10px 0 10px;border:0;">
							<span class="big"><?php echo __('Support YAPB', 'yapb') ?></span>
							<p><?php echo __('Do you like YAPB? Do you use it regulary to show your photos or images? Did YAPB save you time? Or you just want to give something back for the time spent to create, maintain and support YAPB since 2006?', 'yapb') ?></p>
							<p><?php echo __('Just donate a little amount so i may buy a good book, DVD or just pay some server traffic:', 'yapb') ?></p>
							<p>
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
									<input type="hidden" name="cmd" value="_xclick">
									<input type="hidden" name="business" value="paypal@johannes.jarolim.com">
									<input type="hidden" name="item_name" value="A donation for Yet Another Photoblog">
									<input type="hidden" name="item_number" value="1">
									<input type="hidden" name="no_shipping" value="2">
									<input type="hidden" name="no_note" value="1">
									<input type="hidden" name="currency_code" value="EUR">
									<input type="hidden" name="tax" value="0">
									<input type="hidden" name="lc" value="AT">
									<input type="hidden" name="bn" value="PP-DonationsBF">
									<input type="image" src="<?php echo YAPB_PLUGIN_PATH ?>tpl/paypal-donate.gif" border="0" name="submit" alt="Donate with PayPal - fast, free and secure!" style="border:0;">
								</form>
							</p>
							<p><?php echo __('Thanks a lot from Salzburg!', 'yapb') ?></p>
						</td>

					</tr>
				</table>

			</td>

		</tr>

	</table>

	<!-- All Options -->

	<?php

		// We attach an timestamp to the form action URL so 
		// we always see accurate data

		$requestURI = $_SERVER['REQUEST_URI'];
		$requestParameters = array();
		$requestParameters[] = 'nocache=' . time();
		$requestParameters[] = 'page=' . Params::get('page', '');
		$requestURI .= ((strpos($requestURI, '?') === false) ? '?' : '&') . implode('&', $requestParameters);
		
	?>

	<form method="post" action="<?php echo $requestURI ?>">

		<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"> 
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="tabindex" id="tabindex" value="false" />
		<input type="hidden" name="accordionindex" id="accordionindex" value="<?php echo ($this->active+1) ?>" />

		<?php echo $this->options->toString() ?>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Save Changes') ?> &raquo;" /> 
		</p>

	</form>
	

	<!-- /All Options -->

	<div id="debug"></div>

</div>