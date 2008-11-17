<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The ReusableOption:: is the parent class of all options. Options are implemented
 * with a Composite pattern. ReusableOption:: is the Component object of the pattern. 
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class ReusableOption
{
	
	/**
	 * Name of the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_name;
	
	/**
	 * Value of the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_value;
	
	/**
	 * Default value of the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_default;
	
	/**
	 * Label for the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_label;
	
	/**
	 * Text to display before the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_textBefore;
	
	/**
	 * Text to display after the option.
	 * 
	 * @var string
	 * @access private
	 */
	var $_textAfter;
	
	/**
	 * PHP4 type constructor
	 */
	function ReusableOption($name, $defaultValue, $label = '', $textBefore = '', $textAfter = '')
	{
		$this->__construct($name, $defaultValue, $label, $textBefore, $textAfter);
	}
	
	
	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue, $label = '', $textBefore = '', $textAfter = '')
	{	
		$this->_name = $name;
		$this->setDefaultValue($defaultValue);
		$this->setValue($this->_default);
		$this->setLabel($label);
		$this->setTextBefore($textBefore);
		$this->setTextAfter($textAfter);
	}
	
	/**
	 * Abstract implementation of the accept() method allowing traversal of 
	 * options by a visitor object. Subclasses should override this and call the
	 * appropriate visit method on the visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		return false;
	}
	
	/**
	 * Check whether we find a value for this option in the array pulled from 
	 * the database. If so adopt this value.
	 * 
	 * @param array $storedOptions		Array pulled from database.
	 * @access public
	 */
	function load($storedOptions)
	{
		if(is_array($storedOptions)){
			if(array_key_exists($this->_name, $storedOptions))
				$this->setValue($storedOptions[$this->_name]);
		}	
		elseif($storedOptions) //option was not stored in an associative array
			$this->setValue($storedOptions);
	}
	
	/**
	 * Gets an array of options to be stored in the database. Recursively obtains
	 * options from children.
	 * 
	 * @return array string		Array of options to store in database.
	 * @access public
	 */
	function store()
	{
		$result = array();
		$result[$this->_name] = $this->_value;
		
		return $result;
	}
	
	/**
	 * Composite options should overwrite this to allow adding options to them.
	 * 
	 * @param object ReusableOption &$option  The option to be added to the composite.
	 * @return boolean	True if options could be added (composite), false otherwise.
	 * @access public
	 */
	function addChild(&$option)
	{
		return false;
	}
	
	function removeChild($name)
	{
		return false;
	}
	
	
	function countChildren(){
		return 0;
	}

	function &getChild($int)
	{
		$option = null;
		return $option;
	}

	/**
	 * Add an input valdiation test.	
	 * 
	 * @param object InputValidationTest &$test  The test to be added.
	 * @return boolean	True if test could be added, false otherwise.
	 * @access public
	 */
	function addTest(&$test)
	{	
		return false;
	}
	
	/**
	 * Default implementation of the validate() method allowing input validation of 
	 * options.
	 * 
	 * @return array string			The error messages created by the validation procedure.
	 * @access public
	 */
	function validate()
	{	
		return array();
	}
	
	
	
	/**
	 * Looks for an option with a given name.
	 *
	 * @param string $name				The name of the option we are looking for.
	 * @return object ReusableOption	The option if found, null otherwise.
	 * @access public
	 *
	 */
	function &getOptionByName($name){
		$option = null;
		if($this->_name == $name)
			$option = $this;
		
		return $option;
	}
	
	/**
	 * Getter for name field.
	 * @return string		The name of the option.
	 * @access public
	 */
	function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Setter for name field.
	 * @param string $value		The name of the option.
	 * @access public
	 */
	function setOptionName($value)
	{
		$this->_name = $value;
	}
	
	/**
	 * Getter for value field.
	 * @return string		The value of the option.
	 * @access public
	 */
	function getValue()
	{
		return $this->_value;
	}
	
	
	/**
	 * Setter for value field.
	 * @param mixed		The new value of the option.
	 * @access public
	 */
	function setValue($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Setter for default field.
	 * @param mixed		The new default value of the option.
	 * @access public
	 */
	function setDefaultValue($value)
	{
		$this->_default = $value;
	}
	
	/**
	 * Getter for textBefore field.
	 * @return string		Text to show before the option.
	 * @access public
	 */
	function getTextBefore()
	{
		return $this->_textBefore;
	}
	
	
	/**
	 * Setter for textBefore field.
	 * @param string $value	Text to show before the option.
	 * @access public
	 */
	function setTextBefore($value)
	{
		$this->_textBefore = $value;
	}
	
	/**
	 * Getter for textAfter field.
	 * @return string		Text to show after the option.
	 * @access public
	 */
	function getTextAfter()
	{
		return $this->_textAfter;
	}
	
	/**
	 * Setter for textAfter field.
	 * @param string $value	Text to show after the option.
	 * @access public
	 */
	function setTextAfter($value)
	{
		$this->_textAfter = $value;
	}
	
	/**
	 * Getter for label field.
	 * @return string		Option label.
	 * @access public
	 */
	function getLabel()
	{
		return $this->_label;
	}
	
	/**
	 * Setter for label field.
	 * @param string $value		Option label.
	 * @access public
	 */
	function setLabel($value)
	{
		$this->_label = $value;
	}
	


}

?>