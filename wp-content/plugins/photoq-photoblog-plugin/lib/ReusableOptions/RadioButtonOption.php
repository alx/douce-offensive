<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The RadioButtonOption:: class represents a single radio button.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class RadioButtonOption extends SelectableCompositeOption
{
	
	
	/**
	 * PHP4 type constructor
	 */
	function RadioButtonOption($defaultValue, $label = '', 
					$textBefore = '', $textAfter = '')
	{
		$this->__construct($defaultValue, $label, $textBefore, $textAfter);
	}
	
	
	/**
	 * PHP5 type constructor
	 */
	function __construct($defaultValue, $label = '', 
					$textBefore = '', $textAfter = '')
	{
		parent::__construct('', $defaultValue, $label, $textBefore, $textAfter);
	}
	
	
	/**
	 * Concrete implementation of the accept() method. Calls visitRadioButton() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitRadioButton($this);
		parent::accept($visitor);
	}
	
	
}

?>
