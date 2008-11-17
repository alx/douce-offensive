<?php
/**
 * @package ReusableOptions
 */
 

/**
 * A RO_CheckBoxList:: is a container for RO_CheckBoxListOptions. Several of which
 * can be selected at a time.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class RO_CheckBoxList extends SelectionList
{

	
	/**
	 * Add an option to the composite.	
	 * 
	 * @param object ReusableOption &$option  The option to be added to the composite.
	 * @return boolean	True if options could be added (composite), false otherwise.
	 * @access public
	 */
	function addChild(&$option)
	{	
		if(is_a($option, 'RO_CheckBoxListOption')){
			//all checkboxes in a list must have the name of the group
			$option->setOptionName($this->getName());		
			return parent::addChild($option);
		}
		
		return false;
	}
	
	
	
	/**
	 * Concrete implementation of the accept() method. Calls visitCheckBoxList() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitCheckBoxList($this);
		parent::accept($visitor);
	}
	

}


/**
 * The RO_CheckBoxListOption:: class represents a single check box that goes into above list.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class RO_CheckBoxListOption extends SelectableOption
{
	
	
	/**
	 * PHP4 type constructor
	 */
	function RO_CheckBoxListOption($defaultValue, $label = '', 
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
	 * Concrete implementation of the accept() method. Calls visitCheckBoxListOption() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		$visitor->visitCheckBoxListOption($this);
		parent::accept($visitor);
	}
	
	
}

?>
