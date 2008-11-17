<?php
/**
 * @package ReusableOptions
 */


/**
 * A DropDownList:: is a container for DropDownOptions. Only one of which
 * can be selected at a time.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class DropDownList extends SelectionList
{

	/**
	 * Concrete implementation of the accept() method. Calls visitDropDownList() on
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitDropDownListBefore($this);
		parent::accept($visitor);
		$visitor->visitDropDownListAfter($this);
	}

	
	/**
	 * Populate List with children given by name-value array.
	 *
	 * @access public
	 * @param array $nameValueArray Name-value pairs with which to populate the list.
	 */
	function populate($nameValueArray)
	{
		//populate the list with all ImageSizes
		foreach ($nameValueArray as $name => $value){
			$this->addChild(
			new DropDownOption(
			$name, $value
			)
			);
		}
	}



}

?>
