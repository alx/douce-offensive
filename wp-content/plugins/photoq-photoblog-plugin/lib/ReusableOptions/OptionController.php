<?php
/**
 * @package ReusableOptions
 */

//import all reusable option classes
if (!class_exists("ReusableOption")) {
	define('REUSABLEOPTIONS_PATH', dirname(__FILE__).'/');
	
	require_once(REUSABLEOPTIONS_PATH.'OptionVisitor.php');
	require_once(REUSABLEOPTIONS_PATH.'RenderOptionVisitor.php');
	require_once(REUSABLEOPTIONS_PATH.'UpdateOptionVisitor.php');

	
	require_once(REUSABLEOPTIONS_PATH.'ReusableOption.php');
	require_once(REUSABLEOPTIONS_PATH.'CompositeOption.php');
	require_once(REUSABLEOPTIONS_PATH.'RO_ChangeTrackingContainer.php');
	
	require_once(REUSABLEOPTIONS_PATH.'SelectionList.php');
	require_once(REUSABLEOPTIONS_PATH.'SelectableOption.php');
	require_once(REUSABLEOPTIONS_PATH.'SelectableCompositeOption.php');
	
	
	require_once(REUSABLEOPTIONS_PATH.'TextFieldOption.php');
	require_once(REUSABLEOPTIONS_PATH.'PasswordTextFieldOption.php');
	require_once(REUSABLEOPTIONS_PATH.'TextAreaOption.php');
	require_once(REUSABLEOPTIONS_PATH.'HiddenInputFieldOption.php');
	require_once(REUSABLEOPTIONS_PATH.'CheckBoxOption.php');
	require_once(REUSABLEOPTIONS_PATH.'RO_CheckBoxList.php');
	require_once(REUSABLEOPTIONS_PATH.'RadioButtonOption.php');
	require_once(REUSABLEOPTIONS_PATH.'RadioButtonList.php');
	require_once(REUSABLEOPTIONS_PATH.'DropDownOption.php');
	require_once(REUSABLEOPTIONS_PATH.'DropDownList.php');	
	require_once(REUSABLEOPTIONS_PATH.'AuthorDropDownList.php');	
	require_once(REUSABLEOPTIONS_PATH.'CategoryDropDownList.php');	
	
	require_once(REUSABLEOPTIONS_PATH.'Tests/InputTest.php');	
	require_once(REUSABLEOPTIONS_PATH.'Tests/DirExistsInputTest.php');	
	require_once(REUSABLEOPTIONS_PATH.'Tests/FileWritableInputTest.php');	
	require_once(REUSABLEOPTIONS_PATH.'Tests/RO_PHPInstallTests.php');	
	require_once(REUSABLEOPTIONS_PATH.'Tests/RO_WordPressVersionInputTest.php');	
	
}

 

/**
 * The OptionController:: class manages everything related to WordPress options:
 * Loading, Saving, Updating.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class OptionController
{

	/**
	 * Array of options used by this plugin.
	 * @var Array
	 * @access private
	 */
	var $_options;
	
	/**
	 * Options are stored under this name in Wordpress Database.
	 * @var string
	 * @access private
	 */
	var $_optionDBName;
	
	/**
	 * The visitor object used to render options.
	 * @var object RenderOptionVisitor
	 * @access private
	 */
	var $_renderOptionVisitor;
	
	/**
	 * The visitor object used to update options.
	 * @var object UpdateOptionVisitor
	 * @access private
	 */
	var $_updateOptionVisitor;
	
	/**
	 * Any tests that are not related to some specific input field but that 
	 * the current wordpress installation should pass.
	 *
	 * @var array object InputTest
	 * @access private
	 */
	var $_tests;
	
	/**
	 * PHP4 type constructor
	 *
	 * @param string $name	The options will be stored under this name in the 
	 * 						WordPress Database.	
	 * @param object RenderOptionVisitor &$renderVisitor		The visitor object 
	 * 														used to render options.
	 * @access public
	 */
	function OptionController($name, $renderVisitor = '', $updateVisitor = '')
	{
		$this->__construct($name, $renderVisitor, $updateVisitor);
	}
	
	
	/**
	 * PHP5 type constructor
	 *
	 * @param string $name	The options will be stored under this name in the 
	 * 						WordPress Database.	
	 * @param object RenderOptionVisitor &$renderVisitor		The visitor object 
	 * 														used to render options.
	 * @access public
	 */
	function __construct($name, $renderVisitor = '', $updateVisitor = '')
	{
		$this->_options = array();
		$this->_optionsDBName = $name;

		if( $renderVisitor === '')
			$this->_renderOptionVisitor = new RenderOptionVisitor();
		else
			$this->_renderOptionVisitor = $renderVisitor;
		
		if( $updateVisitor === '')
			$this->_updateOptionVisitor = new UpdateOptionVisitor();
		else
			$this->_updateOptionVisitor = $updateVisitor;
			
		$this->_tests = array();
	}
	
	/**
	 * Add an option to the array of options.
	 * @param object ReusableOption &$option	The option to be added.
	 *
	 * @return boolean		True if option was added, False if not.
	 * @access public
	 */
	function registerOption(&$option)
	{
		if(!array_key_exists($option->getName(), $this->_options)){
			$this->_options[$option->getName()] =& $option;
		}else
			return false;
	}
	
	/**
	 * Remove an option from the array of options.
	 * @param mixed $option		The option to be removed or alternatively 
	 *							the name of the option to be removed.
	 * @access public
	 */
	function unregisterOption($option)
	{
		$key = null;
		if(is_object($option) && is_a($option, ReusableOption))
			$key = $option->getName();
		elseif(is_string($option))
			$key = $option;
		if($key)
			unset($this->_options[$key]);
	}
	
	/**
	 * Render an option.
	 *
	 * @param string $optionName	The name of the option to be rendered.
	 * @access public
	 *
	 */
	function render($optionName)
	{
		//$v = new RenderOptionVisitor();
		if(!array_key_exists($optionName, $this->_options))
			echo "<strong>Error in OptionController::render():</strong> 
							no option with name '$optionName' is registered";
		else
			$this->_options[$optionName]->accept($this->_renderOptionVisitor);
		
		//$this->_options[$optionName]->render();
	}
	
	/**
	 * Load all options from database.
	 * @access public
	 */
	function load()
	{
		$storedOptions = get_option($this->_optionsDBName);
		if(!empty($storedOptions)){
			//foreach ($this->_options as $option){
			foreach ( array_keys($this->_options) as $index ) {
				$option =& $this->_options[$index];
				if(array_key_exists($option->getName(), $storedOptions))
				$option->load($storedOptions[$option->getName()]);
			}
		}
	}
	
	/**
	 * Store plugin options in database.
	 * @access private
	 *
	 */
	function _store()
	{
		$optionArray = array();
		//foreach ($this->_options as $option){
		foreach ( array_keys($this->_options) as $index ) {
			$option =& $this->_options[$index];
			$optionArray[$option->getName()] = $option->store();
		}
		update_option($this->_optionsDBName, $optionArray);
	}
	
	/**
	 * Update all options after form submission.
	 * @return array string		The error messages created by input validation.
	 * @access public
	 *
	 */
	function update()
	{
		//foreach ($this->_options as $option){
		foreach ( array_keys($this->_options) as $index ) {
			$option =& $this->_options[$index];
			//$option->update();
			$option->accept($this->_updateOptionVisitor);
		}
		$result = $this->validate();
		$this->_store();
		return $result;
	}
	
	/**
	 * Allows validation of options to be called explictly.
	 * @return array string			The status messages created by the validation procedure.
	 * @access public
	 *
	 */
	function validate()
	{
		$result = array();
		//first we check general tests not associated with specific options.
		$result = array_merge($result, $this->_validateGeneral());
		//next are all the test associated with options.
		//foreach ($this->_options as $option){
		foreach ( array_keys($this->_options) as $index ) {
			$option =& $this->_options[$index];
			$result = array_merge($result, $option->validate());
		}
		return $result;
	}
	
	/**
	 * Validate general tests not associated with a specific option.
	 * 
	 * @return array string			The status messages created by the validation procedure.
	 * @access private
	 */
	function _validateGeneral()
	{
		$result = array();
		foreach ( array_keys($this->_tests) as $index ) {
			$test =& $this->_tests[$index];
			if($statusMsg = $test->validate($this)){
				$result[] = $statusMsg;	
			}
		}	
		return $result;
	}
	
	
	/**
	 * Add an general test to the controller.	
	 * 
	 * @param object InputValidationTest &$test  The test to be added.
	 * @return boolean	True if test could be added, false otherwise.
	 * @access public
	 */
	function addTest(&$test)
	{	
		$this->_tests[] =& $test;
		return true;
	}
	
	/**
	 * Gets the value of the option specified.
	 * @param string $optionName	The name of the option to retrieve.
	 * @access public
	 *
	 */
	function getValue($optionName)
	{
		$result = null;
		//if(array_key_exists($optionName, $this->_options))
		//	$result $this->_options[$optionName]->getValue();
		//else{
			//foreach ($this->_options as $option){
			foreach ( array_keys($this->_options) as $index ) {
				$option =& $this->_options[$index];
				$foundOption =& $option->getOptionByName($optionName);
				if($foundOption){
					$result = $foundOption->getValue();
					break;
				}
			}	
		//}	
			
		return $result;
	}
	
	/**
	 * Setter for the renderOptionVisitor Field.
	 *
	 * @param object RenderOptionVisitor &$visitor	The visitor object responsible
	 * 											  	for rendering the options.
	 * @access public
	 */
	function setRenderOptionVisitor(&$visitor)
	{
		$this->_renderOptionVisitor =& $visitor;
	}


}

?>
