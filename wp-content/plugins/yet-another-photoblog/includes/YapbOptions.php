<?php

	// Option helper classes

	require_once realpath(dirname(__file__) . '/../lib/options/YapbCheckboxInputOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbCheckboxOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbCheckboxSelectOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbExifTagnamesOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbInputOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbSelectOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbTextareaOption.class.php');
	require_once realpath(dirname(__file__) . '/../lib/options/YapbOptionGroup.class.php');

	// The actual list of options
	
	$this->options = new YapbOptionGroup(

		__('YAPB Main Plugin'),
		'',
		array(

			new YapbOptionGroup(
				__('Yet Another Photoblog Options', 'yapb'),
				__('Welcome to YAPB and to it\'s numerous configuration possibilities.<br/>Don\'t panic ;-)', 'yapb'),
				array(

					new YapbOptionGroup(
						__('Writing Options', 'yapb'),
						__('These settings do alter the behaviour of the WordPress input mask for new articles.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_check_post_date_from_exif', __('Check by default: Post date from image exif data if available.', 'yapb'), true),
							new YapbCheckboxSelectOption('yapb_default_post_category', __('Assign post exclusivly to category # if attaching an YAPB-image.', 'yapb'), $this->_options_categories_array(), false, ''),
							new YapbCheckboxOption('yapb_form_on_page_form', __('Enable YAPB-Imageupload for content pages', 'yapb'), false)
						)
					),

					new YapbOptionGroup(
						__('EXIF Filtering Options', 'yapb'), 
						__('EXIF Tags don\'t get displayed by default: Have a look how to adapt your theme manually to show them on your page.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_filter_exif_data', __('Enable EXIF tags filtering', 'yapb'), false),
							new YapbExifTagnamesOption('yapb_view_exif_tagnames', __('List only the following EXIF tags if available:', 'yapb'), array())
						)
					),
					new YapbOptionGroup(
						'Update Services', 
						'',
						array(
							new YapbTextareaOption('yapb_ping_sites', __('YAPB notifies the following site update services if you publish a photoblog-post.<br />These services will be pinged additionally to the services defined on the options/write admin-panel.<br />Separate multiple service URIs with line breaks.', 'yapb'), '')
						)
					)
				)
			),

			new YapbOptionGroup(
				__('Thumbnailer Options', 'yapb'), 
				__('<a href="http://phpthumb.sourceforge.net/" target="_blank">phpThumb</a> is the thumbnailing library of my choice. For your comfort, i made available a selection of settings: For more Information please refer to <a href="http://phpthumb.sourceforge.net" target="_blank">http://phpthumb.sourceforge.net</a> - Especially this two pages: <a href="http://phpthumb.sourceforge.net/demo/docs/phpthumb.readme.txt" target="_blank">readme</a> and <a href="http://phpthumb.sourceforge.net/demo/docs/phpthumb.faq.txt" target="_blank">faq</a>.', 'yapb'),
				array(
				
					new YapbOptionGroup(
						__('ImageMagick configuration', 'yapb'),
						__('If source image is larger than available memory limits AND <a href="http://www.imagemagick.org" target="_blank">ImageMagick\'s "convert" program</a> is available on your server, phpThumb() will call ImageMagick to perform the thumbnailing of the source image to bypass the memory limitation.', 'yapb'),
						array(
							new YapbInputOption('yapb_phpthumb_imagemagick_path', __('Absolute pathname to "convert": #20 Leave empty if "convert" is in the path.', 'yapb'), '')
						)
					),
					new YapbOptionGroup(
						__('Default output configuration', 'yapb'), 
						'',
						array(
							new YapbCheckboxOption('yapb_display_images_xhtml', __('Output Thumbnail URLs XHTML compatible (&amp;amp; instead of &)', 'yapb'), true),
							new YapbSelectOption('yapb_phpthumb_output_format', __('Default output format: # Thumbnail will be output in this format (if available in your version of GD).', 'yapb'), array('JPG' => 'jpeg', 'PNG' => 'png', 'GIF' => 'gif'), 'jpeg'),
							new YapbCheckboxOption('yapb_phpthumb_output_interlace', __('Interlaced output for GIF/PNG, progressive output for JPEG; if unchecked: non-interlaced for GIF/PNG, baseline for JPEG.', 'yapb'), false)
						)
					)
				)
			),

			new YapbOptionGroup(
				__('Feed Options', 'yapb'), 
				__('Here you may alter the behaviour of the automatic feed insertion.', 'yapb'),
				array(

					new YapbOptionGroup(
						__('Embedding', 'yapb'),
						__('YAPB may embed images/thumbnails in your RSS2 and ATOM feeds.<br/>You will have to turn on this feature if you want to subscribe to services like <a href="http://photos.vfxy.com" target="_blank">VFXY</a>.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_xml', __('<strong>Embed images in RSS2 and ATOM feeds content.</strong>', 'yapb'), true),
							new YapbInputOption('yapb_display_images_xml_inline_style', __('Inline CSS-Style for image tag: #40', 'yapb'), 'float:left;padding:0 10px 10px 0;'),
							new YapbTextareaOption('yapb_display_images_xml_html_before', __('Custom HTML before image tag', 'yapb'), ''),
							new YapbTextareaOption('yapb_display_images_xml_html_after', __('Custom HTML after image tag', 'yapb'), '')
						)
					),
				
					new YapbOptionGroup(
						__('Format', 'yapb'),
						__('Set the maximum width and height of the thumbnail inserted into your feed:<ul><li>If you set either width or height, the other value will be calculated based on the actual image size to preserve the image proportions.</li><li>If you set both, YAPB tries to define width and height so the entire image fits into your defined rectangle.</li><li>If you check the crop-option, YAPB crops the thumbnail (if neccessary) so it fills the rectangle entirely.</li></ul>', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_xml_thumbnail_activate', __('Embed as thumbnail', 'yapb'), true),
							new YapbInputOption('yapb_display_images_xml_thumbnail', __('Maximum thumbnail width of #3 px', 'yapb'), '180'),
							new YapbInputOption('yapb_display_images_xml_thumbnail_height', __('Maximum thumbnail height of #3 px', 'yapb'), ''),
							new YapbCheckboxOption('yapb_display_images_xml_thumbnail_crop', __('Crop thumbnail to fill in the defined rectangle', 'yapb'), false)
						)
					)

				)
			),



			new YapbOptionGroup(
				__('Automatic Image Insertion', 'yapb'),
				__('Yapb does display uploaded images automatically on different sections of your site by default.<br/>That\'s just a help for first-time-users and evaluation purproses: To style your photoblog individually,<br/> turn off this option and have a look at <a target="_blank" href="http://johannes.jarolim.com/blog/wordpress/yet-another-photoblog/adapting-templates/">how to adapt themes manually</a>.', 'yapb'),
				array(

					new YapbOptionGroup(
						__('General', 'yapb'), 
						'',
						array(
							new YapbCheckboxOption('yapb_display_images_activate', '<strong>' . __('Activate automatic image rendering in general.', 'yapb') . '</strong>', true),
							new YapbCheckboxSelectOption('yapb_display_images_linked', __('Link thumbnails to actual post and open page #', 'yapb'), array(__('without target', 'yapb') => 'nA',  __('in the same window','yapb') => '_self', __('in a new window','yapb') => '_blank'), true, 'nA')
						)
					),

					new YapbOptionGroup(
						__('Home page', 'yapb'), 
						__('The homepage usually shows a number of previously published posts.<br />You probably want to show thumbnails only.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_home', __('<strong>Display images on HOME page listing.</strong>', 'yapb'), true),
							new YapbCheckboxInputOption('yapb_display_images_home_thumbnail', __('Display as thumbnail with a width of #3 px', 'yapb'), true, '200'),
							new YapbInputOption('yapb_display_images_home_inline_style', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
							new YapbTextareaOption('yapb_display_images_home_html_before', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
							new YapbTextareaOption('yapb_display_images_home_html_after', __('Custom HTML after image tag', 'yapb'), '</div>')
						)
					),

					new YapbOptionGroup(
						__('Single Pages', 'yapb'),
						__('A single page shows a published post on its own.<br />You probably want to show the whole image -<br />But you can use thumbnailing here too, if you have design restrictions for example.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_single', __('<strong>Display images on SINGLE pages.</strong>', 'yapb'), true),
							new YapbCheckboxInputOption('yapb_display_images_single_thumbnail', __('Display as thumbnail with a width of #3 px', 'yapb'), true, '460'),
							new YapbInputOption('yapb_display_images_single_inline_style', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
							new YapbTextareaOption('yapb_display_images_single_html_before', __('Custom HTML before image tag', 'yapb'), '<div style="margin-bottom:20px;">'),
							new YapbTextareaOption('yapb_display_images_single_html_after', __('Custom HTML after image tag', 'yapb'), '</div>')
						)
					),

					new YapbOptionGroup(
						__('Archive Pages', 'yapb'),
						__('Archive pages usually show an overview of all published posts in a category, date range, etc.<br />You probably want to use thumbnails here.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_archive', __('<strong>Display images on ARCHIVE overview page listings.</strong>', 'yapb'), true),
							new YapbCheckboxInputOption('yapb_display_images_archive_thumbnail', __('Display as thumbnail with a width of #3 px', 'yapb'), true, '100'),
							new YapbInputOption('yapb_display_images_archive_inline_style', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
							new YapbTextareaOption('yapb_display_images_archive_html_before', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
							new YapbTextareaOption('yapb_display_images_archive_html_after', __('Custom HTML after image tag', 'yapb'), '</div>')
						)
					),

					new YapbOptionGroup(
						__('Content Pages', 'yapb'),
						__('You may post images to your content pages if you activate the according option above.<br />On content pages you probably want to show the original image.', 'yapb'),
						array(
							new YapbCheckboxOption('yapb_display_images_page', __('<strong>Display images on CONTENT pages.</strong>', 'yapb'), true),
							new YapbCheckboxInputOption('yapb_display_images_page_thumbnail', __('Display as thumbnail with a width of #3 px', 'yapb'), false, '100'),
							new YapbInputOption('yapb_display_images_page_inline_style', __('Inline CSS-Style for image tag: #40', 'yapb'), ''),
							new YapbTextareaOption('yapb_display_images_page_html_before', __('Custom HTML before image tag', 'yapb'), '<div style="float:left;border:10px solid silver;margin-right:10px;margin-bottom:10px;">'),
							new YapbTextareaOption('yapb_display_images_page_html_after', __('Custom HTML after image tag', 'yapb'), '</div>')
						)
					)

				)
			)

		)
	);

	// Run YAPB Options filter

	$this->options = apply_filters('yapb_options', $this->options);


?>