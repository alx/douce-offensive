<?php
/**
 * @package ReusableOptions
 */

/**
 * The DropDownOption:: class represents a single radio button.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class DropDownOption extends SelectableOption
{
	
	/**
	 * Concrete implementation of the accept() method. Calls visitDropDownOption() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitDropDownOption($this);
	}
	
}

?>
