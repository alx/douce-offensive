<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The ReusableOption:: is the parent class of all options. Component class of 
 * the options Composite pattern.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class HiddenInputFieldOption extends ReusableOption
{

	
	/**
	 * PHP4 type constructor
	 */
	function HiddenInputFieldOption($name, $defaultValue)
	{
		$this->__construct($name, $defaultValue);
	}
	
	
	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue)
	{
		parent::__construct($name, $defaultValue, '','','');
	}
	
	/**
	 * Concrete implementation of the accept() method. Calls visitHiddenInputField() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitHiddenInputField($this);
	}
	
	
}

?>
