<?php

/**
 * Option controller subclass responsible for hanlding options of the PhotoQ plugin.
 * @author: M. Flury
 * @package: PhotoQ
 *
 */
class PhotoQOptionController extends OptionController
{
	var $ORIGINAL_IDENTIFIER = 'original';
	var $THUMB_IDENTIFIER = 'thumbnail';
	var $MAIN_IDENTIFIER = 'main';
	
	/**
	 * PHP4 type constructor
	 *	
	 * @access public
	 */
	function PhotoQOptionController()
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
		parent::__construct("wimpq_options", new PhotoQRenderOptionVisitor());
		
		//get alternative original identifier if available
		$originalID = get_option( "wimpq_originalFolder" );
		if($originalID)
			$this->ORIGINAL_IDENTIFIER = $originalID;
			
		
		//establish default options
		$this->_defineAndRegisterOptions();
		
	}
	
	
	
	/**
	 * Defines all the plugin options and registers them with the OptionController.
	 *
	 * @access private
	 */
	function _defineAndRegisterOptions()
	{
		
		//define general tests not associated to options but that should be passed
		$this->addTest(new RO_SafeModeOffInputTest());
		$this->addTest(new RO_GDAvailableInputTest());
		$this->addTest(new RO_WordPressVersionInputTest('2.5.1','2.6-beta3'));
		
		
		$exif =& new RO_ChangeTrackingContainer('exifOptions');
		//define exif options (inside tracker so that we see changes)
		$exifTags =& new RO_CheckBoxList(
			'exifTags',
			''
		);

		if($tags = get_option( "wimpq_exif_tags" )){
			foreach($tags as $key => $value){
				$exifTags->addChild(
					new RO_CheckBoxListOption($key, "$key <br/>( $value )", '<li>', '</li>')
				);
			}
		}
		$exif->addChild($exifTags);
		$this->registerOption($exif);
		
		//watermark options
		$watermark =& new RO_ChangeTrackingContainer('watermarkOptions');
		$watermarkPosition =& new RadioButtonList(
				'watermarkPosition',
				'BL',
				'',
				'<tr valign="top"><th scope="row">Position: </th><td>',
				'</td></tr>'
		);
		$valueLabelArray = array(
			'BR' => 'Bottom Right',
			'BL' => 'Bottom Left',
			'TR' => 'Top Right',
			'TL' => 'Top Left',
			'C' => 'Center',
			'R' => 'Right',
			'L' => 'Left',
			'T' => 'Top',
			'B' => 'Bottom',
			'*' => 'Tile'
		);
		$watermarkPosition->populate($valueLabelArray);
		$watermark->addChild($watermarkPosition);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkOpacity',
				'100',
				'',
				'<tr valign="top"><th scope="row">Opacity: </th><td>',
				'%</td></tr>',
				'2'
			)
		);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkXMargin',
				'20',
				'left/right:',
				'<tr valign="top"><th scope="row">Margins: </th><td>',
				'px, ',
				'2',
				'2'
			)
		);
		
		$watermark->addChild(
			new TextFieldOption(
				'watermarkYMargin',
				'20',
				'top/bottom:',
				'',
				'px<br/>(Values smaller than one are interpreted as percentages instead of pixels.)</td></tr>',
				'2',
				'2'
			)
		);
		
		$this->registerOption($watermark);
		
		//build field checkbox options
		$this->registerOption(
			new CheckBoxOption(
				'fieldAddPosted',
				'0',
				'Add to already posted as well.'
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'fieldDeletePosted',
				'0',
				'Delete from already posted as well.'
			)
		);
		$this->registerOption(
			new CheckBoxOption(
				'fieldRenamePosted',
				'1',
				'Rename already posted as well.'
			)
		);

		

		//build and register further options
		
		//$imgDirOption =& new RO_ChangeTrackingContainer('imgDirOption');
		$imgdir =& new TextFieldOption(
				'imgdir',
				'wp-content',
				'',
				'',
				'<br />Default is <code>wp-content</code>'
		);
		$imgdir->addTest(new DirExistsInputTest('',
			'Image Directory not found: '));
		$imgdir->addTest(new FileWritableInputTest('',
			'Image Directory not writable: '));
		//$imgDirOption->addChild($imgdir);	
		$this->registerOption($imgdir);
		
		
		$imagemagickPath =& new TextFieldOption(
				'imagemagickPath',
				'',
				'Absolute path to the ImageMagick convert executable. (e.g. <code>/usr/bin/convert</code> ). Leave empty if "convert" is in the path.'
		);
		
		/*$imagemagickPath->addTest(
			new PhotoQImageMagickPathCheckInputTest()
		);*/
		$this->registerOption($imagemagickPath);
		
		
		
		$this->registerOption(
			new TextFieldOption(
				'cronFreq',
				'23',
				'',
				'',
				'hours',
				'3',
				'5'
			)
		);

		$showThumbs =& new CheckBoxOption(
			'showThumbs',
			'1',
			'Show thumbs in post management admin panel. '
		);
		$showThumbs->addChild(
			new TextFieldOption(
				'showThumbs-Width',
				'120',
				'',
				' Maximum ',
				'px wide, ',
				'3',
				'3'
			)
		);
		$showThumbs->addChild(
			new TextFieldOption(
				'showThumbs-Height',
				'60',
				'',
				' ',
				'px high',
				'3',
				'3'
			)
		);
		$this->registerOption($showThumbs);

		$enableFtp =& new CheckBoxOption(
			'enableFtpUploads',
			'0',
			'Allow importing of photos from the following directory on the server: '
		);
		$enableFtp->addChild(
			new TextFieldOption(
				'ftpDir',
				'',
				'',
				'',
				'<br />Full path (e.g., <code>'.ABSPATH.'wp-content/ftp</code>)'
			)
		);
		$this->registerOption($enableFtp);
		
		$this->registerOption(
			new TextFieldOption(
				'postMulti',
				'999',
				'Second post button posts ',
				'',
				' photos at once.',
				'3',
				'3'
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'foldCats',
				'0',
				'Fold away category lists per default.'
			)
		);
		
		$this->registerOption(
			new CheckBoxOption(
				'deleteImgs',
				'1',
				'Delete image files from server when deleting post.'
			)
		);

		$this->registerOption(
			new CheckBoxOption(
				'enableBatchUploads',
				'1',
				'Enable Batch Uploads.'
			)
		);

		$this->registerOption(
			new AuthorDropDownList(
				 'qPostAuthor',
				 '1',
				 'This is the author of the posts posted via PhotoQ.'
			)
		);
		
		$this->registerOption(
			new CategoryDropDownList(
				 'qPostDefaultCat',
				 '1',
				 'This is the default category for posts posted via PhotoQ.'
			)
		);
		
		$imageSizes =& new ImageSizeContainer('imageSizes', array());
		
		$imageSizes->addChild(new ImageSizeOption($this->THUMB_IDENTIFIER, '0', '80', '60'));
		$imageSizes->addChild(new ImageSizeOption($this->MAIN_IDENTIFIER, '0'));
		
		$this->registerOption($imageSizes);
		
		
		$originalFolder =& new RO_ChangeTrackingContainer('originalFolder');
		$originalFolder->addChild(
			new CheckBoxOption(
				'hideOriginals',
				'0',
				'Hide folder containing original photos. If checked, PhotoQ will attribute a random name to the folder.',
				'',
				''
			)
		);
		$this->registerOption($originalFolder);
		
		$contentView =& new PhotoQViewOption(
				'contentView',
				$this->MAIN_IDENTIFIER,
				$this->THUMB_IDENTIFIER
		);
		$contentView->addChild(
			new CheckBoxOption(
				'inlineDescr',
				'1',
				'Include photo description in post content. ',
				'<tr><th>Photo Description:</th><td>',
				'</td></tr>'
			)
		);
		$contentView->addChild(
			new CheckBoxOption(
				'inlineExif',
				'0',
				'Include Exif data in post content. ',
				'<tr><th>Exif Meta Data:</th><td>',
				'</td></tr>'
			)
		);
		$this->registerOption($contentView);
		
		
		$excerptView =& new PhotoQViewOption(
				'excerptView',
				$this->MAIN_IDENTIFIER,
				$this->THUMB_IDENTIFIER
		);
		$this->registerOption($excerptView);
		
		//overwrite default options with saved options from database
		$this->load();
				
		//populate lists of image sizes that depend on runtime stuff and cannot be populated before
		$contentView->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		$excerptView->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
	
		//same for imageMagick test
		//$imagemagickTest = new PhotoQImageMagickPathCheckInputTest();
		//$msg = $imagemagickTest->validate($imagemagickPath);
		//$imagemagickPath->setTextAfter('<br/>'. $msg);
		
		//check for existence of cache directory
		//convert backslashes (windows) to slashes
		$cleanAbs = str_replace('\\', '/', ABSPATH);
		$this->addTest( new DirExistsInputTest(
			preg_replace('#'.$cleanAbs.'#', '', $this->getCacheDir()), 
			'Cache Directory not found: ')
		);
		$this->addTest( new FileWritableInputTest(
			preg_replace('#'.$cleanAbs.'#', '', $this->getCacheDir()), 
			'Cache Directory not writeable: ')
		);
	}
	
	/**
	 * initialize stuff that depends on runtime configuration so that 
	 * what is displayed represents the changes from last update.
	 *
	 */
	function initRuntime()
	{
		//$this->load();
		//populate lists of image sizes that depend on runtime stuff and cannot be populated before
		$this->_options['contentView']->unpopulate();
		$this->_options['excerptView']->unpopulate();
		
		//put the available image sizes into the list for content and excerpt
		$this->_options['contentView']->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		$this->_options['excerptView']->populate($this->getImageSizeNames(),$this->ORIGINAL_IDENTIFIER == 'original');
		
		//test for presence of imageMagick
		$imagemagickTest = new PhotoQImageMagickPathCheckInputTest();
		$msg = $imagemagickTest->validate($this->_options['imagemagickPath']);
		$this->_options['imagemagickPath']->setTextAfter('<br/>'. $msg);
	}
	
	function addImageSize($name)
	{
		$imageSizes =& $this->_options['imageSizes'];
		if($name != 'original' && !array_key_exists($name, $imageSizes->getValue())){
			//add corresponding directory to imgdir
			if(PhotoQHelper::createDir($this->getImgDir() . $name)){
				//add to database
				$imageSizes->addChild(new ImageSizeOption($name));
				$this->_store();
			}else
				return new PhotoQErrorMessage(__("Could not create image size. The required directory in wp-content could not be created. Please check your settings and/or PHP Save Mode."));			
		}else
			return new PhotoQErrorMessage(__("Name already taken, please choose another name."));
		return new PhotoQStatusMessage(__("New image size successfully created."));
	}
	
	function removeImageSize($name)
	{
		$imageSizes =& $this->_options['imageSizes'];
		//remove corresponding dirs from server
		if(PhotoQHelper::recursiveRemoveDir($this->getImgDir() . $name)){
			//remove from database
			$imageSizes->removeChild($name);
			$this->_store();
		}else
				return new PhotoQErrorMessage(__("Could not remove image size. The required directories in wp-content could not be removed. Please check your settings."));
		return new PhotoQStatusMessage(__("Image size successfully removed."));
	}
	
	
	function getQDir(){
		return $this->getImgDir().'qdir/';
	}
	
	/**
	 * Returns the cache directory used by phpThumb. This is now fixed to wp-content/photoQCache.
	 *
	 * @return string	The cache directory.
	 */
	function getCacheDir(){
		return str_replace('\\', '/', ABSPATH) . 'wp-content/photoQCache/';
	}
	
	function getImgDir(){
		//prepend ABSPATH to $imgdir if it is not already there
		$dirPath = str_replace(ABSPATH, '', trim($this->getValue('imgdir')));
		//$dirPath = str_replace(ABSPATH, '', 'wp-content');
		$dir = rtrim(ABSPATH . $dirPath, '/');
		return $dir . '/';
	}
	
	function getFtpDir(){
		return '/'.trim($this->getValue('ftpDir'), '\\/').'/';
	}
	
	function getMainIdentifier()
	{
		return $this->MAIN_IDENTIFIER;
	}
	
	function getThumbIdentifier()
	{
		return $this->THUMB_IDENTIFIER;
	}
	
	function getOriginalIdentifier()
	{
		return $this->ORIGINAL_IDENTIFIER;
	}
	
	/**
	 * Returns an array containing all image sizes.
	 *
	 * @return array	the names of all registered imageSizes
	 */
	function getImageSizeNames()
	{
		return array_keys($this->getValue('imageSizes'));
	}
	
	/**
	 * Returns an array containing names of image sizes that changed during last update.
	 *
	 * @return array	the names of all changed imageSizes
	 */
	function getChangedImageSizeNames()
	{
		$imageSizes =& $this->_options['imageSizes'];
		return $imageSizes->getChangedImageSizeNames();
	}
	
	
	
	/**
	 * Returns a boolean indicating whether options inside indicated container(s) 
	 * changed during last update.
	 *
	 * @return boolean
	 */
	function hasChanged($containerNames)
	{
		if(is_array($containerNames)){
			foreach ($containerNames as $containerName){
				$opt =& $this->_options[$containerName];
				if($opt->hasChanged())
					return true;
			}
			return false;
		}else{
			$opt =& $this->_options[$containerNames];
			return $opt->hasChanged();
		}
	}
	
	function getOldValues($containerName)
	{
		$opt =& $this->_options[$containerName];
		return $opt->_oldValues;
	}
	
	
	

	
	
}


/**
 * The PhotoQRenderOptionVisitor:: is responsible for rendering of the options. It 
 * renders every visited option in HTML.
 *
 * @author  M. Flury
 * @package PhotoQ
 */
class PhotoQRenderOptionVisitor extends RenderOptionVisitor
{
	
	
	 
	/**
	 * Method called whenever a
	 * ImageSizeOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object ImageSizeOption &$dropDownList	Reference to visited option.
	 */
	 function visitImageSizeBefore(&$imageSize)
	 {
	 	$deleteLink = '';
	 	if($imageSize->isRemovable()){
	 		$deleteLink = 'options-general.php?page=whoismanu-photoq.php&amp;action=deleteImgSize&amp;entry='.$imageSize->getName();
	 		$deleteLink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($deleteLink, 'photoq-deleteImgSize' . $imageSize->getName()) : $deleteLink;
	 		$deleteLink = '<a href="'.$deleteLink.'" class="delete" onclick="return confirm(\'Are you sure?\');">Delete</a>';
	 	}
	 	print '<table width="100%" cellspacing="2" cellpadding="5" class="form-table noborder">
	 				<tr valign="top">
	 					<th> ' .$imageSize->getName().'</th>
	 					<td style="text-align:right">'.$deleteLink.'</td>
	 				</tr>';
	 }
	 
	 /**
	 * Method called whenever a
	 * ImageSizeOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object ImageSizeOption &$imageSize	Reference to visited option.
	 */
	 function visitImageSizeAfter(&$imageSize)
	 {
	 	print "</table>";
	 }
	 	
}




class ImageSizeContainer extends CompositeOption
{
	/**
	 * Check whether we find a value for this option in the array pulled from
	 * the database. If so adopt this value. Pass the array on to all the children
	 * such that they can do the same.
	 *
	 * @param array $storedOptions		Array pulled from database.
	 * @access public
	 */
	function load($storedOptions)
	{
		if(is_array($storedOptions)){
			if(array_key_exists($this->getName(), $storedOptions)){
				$this->setValue($storedOptions[$this->getName()]);
			}
			//register all ImageSizes that can be added/removed on runtime
			foreach ($this->getValue() as $key => $value){
				//only add if not yet there and removable
				if(!$this->getOptionByName($key) && $value) $this->addChild(new ImageSizeOption($key, '1'));
			}
			parent::load($storedOptions);
		}
		
		

	}
	
	/**
	 * Stores own values in addition to selected childrens values in associative 
	 * array that can be stored in Wordpress database.
	 * 
	 * @return array $result		Array of options to store in database.
	 * @access public
	 */
	function store()
	{
		$result = array();
		$result[$this->_name] = $this->getValue();
		$result = array_merge($result, parent::store());
		return $result;
	}
	
	/**
	 * Add an option to the composite. And add its name to the list of names (= value of ImageSizeContainer)
	 * 
	 * @param object ReusableOption &$option  The option to be added to the composite.
	 * @return boolean	True if options could be added (composite), false otherwise.
	 * @access public
	 */
	function addChild(&$option)
	{	
		if(is_a($option, 'ImageSizeOption')){
			$newValue = $this->getValue();
			$newValue[$option->getName()] = $option->isRemovable();
			$this->setValue($newValue);
			return parent::addChild($option);
		}
		return false;
	}
	
	/**
	 * Remove an option from the composite.	
	 * 
	 * @param string $name  The option to be removed from the composite.
	 * @return boolean 		True if existed and removed, False otherwise.
	 * @access public
	 */
	function removeChild($name)
	{	
		$newValue = $this->getValue();
		if($newValue[$name]){ //only remove images sizes that are allowed to be removed
			unset($newValue[$name]);
			$this->setValue($newValue);
			return parent::removeChild($name);
		}
		return false;
	}
	
	
	function getChangedImageSizeNames(){
		$changed = array();
		$numChildren = $this->countChildren();
		for ($i = 0; $i < $numChildren; $i++){
			$current =& $this->getChild($i);
			if($current->hasChanged())
				$changed[] = $current->getName();
		}
		return $changed; 
	}

	
}

class ImageSizeOption extends RO_ChangeTrackingContainer
{
	
	/**
	 * Default width of Image size.
	 *
	 * @access private
	 * @var integer
	 */
	var $_defaultWidth;
	
	/**
	 * Default height of Image size.
	 *
	 * @access private
	 * @var integer
	 */
	var $_defaultHeight;
	
	
	
	/**
	 * PHP4 type constructor. $defaultValue determines whether this image size is removable or not.
	 */
	function ImageSizeOption($name, $defaultValue = '1', $defaultWidth = '700', $defaultHeight = '525')
	{
		$this->__construct($name, $defaultValue, $defaultWidth, $defaultHeight);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue = '1', $defaultWidth = '700', $defaultHeight = '525')
	{
		parent::__construct($name, $defaultValue);
		
		$this->_defaultWidth = $defaultWidth;
		$this->_defaultHeight = $defaultHeight;
		
		$this->_buildRadioButtonList();
		/*$this->addChild(
			new CheckBoxOption(
				$this->_name . '-browserResize',
				'0',
				'Resize only in web browser.',
				'<tr valign="top"><th scope="row">&nbsp;</th><td>',
				'</td></tr>'
			)
		);*/
		
		$this->addChild(
			new TextFieldOption(
				$this->_name . '-imgQuality',
				'95',
				'',
				'<tr valign="top"><th scope="row">Image Quality: </th><td>',
				'%</td></tr>',
				'2'
			)
		);
		
		$this->addChild(
			new CheckBoxOption(
				$this->_name . '-watermark',
				'0',
				'Add watermark to all images of this size.',
				'<tr valign="top"><th scope="row">Watermark:</th><td>',
				'</td></tr>'
			)
		);
		
	}
	
	/**
	 * Concrete implementation of the accept() method. Calls visitImageSize() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		if(method_exists($visitor, 'visitImageSizeBefore'))
			$visitor->visitImageSizeBefore($this);
		parent::accept($visitor);
		if(method_exists($visitor, 'visitImageSizeAfter'))
			$visitor->visitImageSizeAfter($this);
	}
	
	
	function _buildRadioButtonList()
	{
		$imgConstr = new RadioButtonList(
				$this->_name . '-imgConstraint',
				'rect'
		);

		$maxDimImg = new RadioButtonOption(
				'rect',
				'Maximum Dimensions: ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$maxDimImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgWidth',
				$this->_defaultWidth,
				'',
				'<td>',
				'px wide, ',
				'4',
				'5'
			)
		);
		$maxDimImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgHeight',
				$this->_defaultHeight,
				'',
				'',
				'px high ',
				'4',
				'5'
			)
		);
		$maxDimImg->addChild(
			new CheckBoxOption(
				$this->_name . '-zoomCrop',
				0,
				'Crop to max. dimension.&nbsp;)',
				'&nbsp;(&nbsp;',
				'</td></tr>'
			)
		);
		$imgConstr->addChild($maxDimImg);


		$smallestSideImg = new RadioButtonOption(
				'side',
				'Smallest side: ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$smallestSideImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgSide',
				'525',
				'',
				'<td>',
				'px</td></tr>',
				'4',
				'5'
			)
		);
		$imgConstr->addChild($smallestSideImg);

		$fixedWidthImg = new RadioButtonOption(
				'fixed',
				'Fixed width: ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$fixedWidthImg->addChild(
			new TextFieldOption(
				$this->_name . '-imgFixed',
				'525',
				'',
				'<td>',
				'px</td></tr>',
				'4',
				'5'
			)
		);
		$imgConstr->addChild($fixedWidthImg);

		$imgConstr->addChild(
			new RadioButtonOption(
				'noResize',
				'Original Size: ',
				'<tr valign="top"><th scope="row">',
				'</th><td>Keep original image size, don\'t resize.</td></tr>'
			)
		);
		
		
		
		$this->addChild($imgConstr);
	}
	
	/**
	 * Tests whether the ImageSize in question is removable of not.
	 *
	 * @return boolean
	 */
	function isRemovable()
	{
		return $this->getValue();
	}
	

}


class PhotoQViewOption extends RO_ChangeTrackingContainer
{
	
	var $_mainID;
	var $_thumbID;
	
	/**
	 * PHP4 type constructor. $defaultValue determines whether this image size is removable or not.
	 */
	function PhotoQViewOption($name, $mainID = '', $thumbID = '')
	{
		$this->__construct($name, $mainID, $thumbID);
	}


	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $mainID, $thumbID)
	{
		parent::__construct($name);
		
		$this->_mainID = $mainID;
		$this->_thumbID = $thumbID;
		
		$this->_buildRadioButtonList();
	}
	
	
	function _buildRadioButtonList()
	{
		$viewType =& new RadioButtonList(
				$this->_name . '-type',
				'single'
		);

		
		$singleImg =& new RadioButtonOption(
				'single',
				'Single Photo: ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$singleSize =& new DropDownList(
				$this->_name . '-singleSize',
				$this->_mainID,
				'',
				'<td>',
				'</td></tr>'
		);
		$singleImg->addChild($singleSize);
		$viewType->addChild($singleImg);
		
		
		$imgLink =& new RadioButtonOption(
				'imgLink',
				'Image Link: ',
				'<tr valign="top"><th scope="row">',
				'</th>'
		);
		$imgLinkSize =& new DropDownList(
				$this->_name . '-imgLinkSize',
				$this->_thumbID,
				'',
				'<td>',
				' linking to '
		);		
		$imgLink->addChild($imgLinkSize);
		$imgLinkTargetSize =& new DropDownList(
				$this->_name . '-imgLinkTargetSize',
				$this->_mainID,
				'',
				'',
				''
		);
		$imgLink->addChild($imgLinkTargetSize);
		
		$imgLink->addChild(
			new TextFieldOption(
				$this->_name . '-imgLinkAttributes',
				attribute_escape('rel="lightbox"'),
				', link having following attributes: ',
				'',
				'<br />
				(Allows interaction with JS libraries such as Lightbox and 
				Shutter Reloaded without modifying templates.)</td></tr>',
				'40'
			)
		);
		
		$viewType->addChild($imgLink);
		
		$this->addChild($viewType);
		
	}
	
	/**
	 * Populate the lists of image sizes with the names of registered image sizes as key, value pair.
	 *
	 * @param array $imgSizeNames
	 * @access public
	 */
	function populate($imgSizeNames, $addOriginal = true)
	{
		//add the original as an option
		if($addOriginal)
			array_push($imgSizeNames,'original');
		
		$singleSize =& $this->getOptionByName($this->_name .'-singleSize');
		$singleSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
		$imgLinkSize =& $this->getOptionByName($this->_name .'-imgLinkSize');
		$imgLinkSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
		$imgLinkTargetSize =& $this->getOptionByName($this->_name .'-imgLinkTargetSize');
		$imgLinkTargetSize->populate(PhotoQHelper::arrayCombine($imgSizeNames,$imgSizeNames));
		
	}
	
	/**
	 * Remove names of registered image sizes as key, value pair.
	 *
	 * @access public
	 */
	function unpopulate()
	{
		$singleSize =& $this->getOptionByName($this->_name .'-singleSize');
		$singleSize->removeChildren();
		$imgLinkSize =& $this->getOptionByName($this->_name .'-imgLinkSize');
		$imgLinkSize->removeChildren();
		$imgLinkTargetSize =& $this->getOptionByName($this->_name .'-imgLinkTargetSize');
		$imgLinkTargetSize->removeChildren();
		
	}
	
	
	
	

}


/**
 * The PhotoQImageMagickPathCheckInputTest:: checks whether 
 * imagemagick path really leads to imagemagick.
 *
 * @author  M.Flury
 * @package PhotoQ
 */
class PhotoQImageMagickPathCheckInputTest extends InputTest
{
	
	/**
	 * Concrete implementation of the validate() method. This methods determines 
	 * whether input validation passes or not.
	 * @param object ReusableOption &$target 	The option to validate.
	 * @return String 	The error message created by this test.
	 * @access public
	 */
	function validate(&$target)
	{	
		$errMsg = '';
		require_once(PHOTOQ_PATH.'lib/phpThumb_1.7.8/phpthumb.class.php');
		// create phpThumb object
		$phpThumb = new phpThumb();
		$phpThumb->config_imagemagick_path = ( $target->getValue() ? $target->getValue() : null );
		//under windows the version check doesn't seem to work so we also check for availability of resize
		if ( !$phpThumb->ImageMagickVersion() && !$phpThumb->ImageMagickSwitchAvailable('resize') ) {
    		$errMsg = "Note: ImageMagick does not seem to be installed at the location you specified. 
    		ImageMagick is optional but might be needed to process bigger photos, plus PhotoQ might run 
    		faster if you setup ImageMagick correctly. If you don't care about ImageMagick and are happy 
    		with using the GD library you can safely ignore this message.";
		}
		return $this->formatErrMsg($errMsg);
	}
	
	
}

?>
