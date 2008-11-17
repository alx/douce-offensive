<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The SelectableOption:: class represents a single selectable option.
 *
 * @author  M.Flury
 * @package ReusableOptions
 */
class SelectableCompositeOption extends CompositeOption
{
	/**
	 * State of the radiobutton
	 * @var boolean
	 * @access private
	 */
	var $_selected;
	
	/**
	 * PHP4 type constructor
	 */
	function SelectableCompositeOption($name, $defaultValue, $label = '', 
					$textBefore = '', $textAfter = '')
	{
		$this->__construct($name, $defaultValue, $label, $textBefore, $textAfter);
	}
	
	
	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue, $label = '', 
					$textBefore = '', $textAfter = '')
	{
		parent::__construct($name, $defaultValue, $label, $textBefore, $textAfter);
		$this->deselect();
	}
	
	
	/**
	 * Select this option.
	 *
	 * @access public
	 */
	function select()
	{
		$this->_selected = true;
	}
	
	/**
	 * Deselect this option.
	 *
	 * @access public
	 */
	
	function deselect()
	{
		$this->_selected = false;
	}
	
	/**
	 * Check whether this option is selected.
	 *
	 * @return boolean	True if selected, False otherwise.
	 * @access public
	 */
	function isSelected()
	{		
		return $this->_selected;
	}

}

?>
