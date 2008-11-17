<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The RO_ChangeTrackingContainer:: is a CompositeOption that acts only as container 
 * for other options, that is it does not really have a value on its own. Any subclass
 * only makes calls on the update option visitor through its parent class. The container
 * is then able to track if any of the options it contains changed its value during the 
 * last update.
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class RO_ChangeTrackingContainer extends CompositeOption
{

	/**
	 * Old values are stored before updating so we can check whether any of them changed
	 *
	 * @access private
	 * @var array
	 */
	var $_oldValues;

	/**
	 * Indicates whether the option has changed in the last update.
	 *
	 * @access private
	 * @var boolean
	 */
	var $_hasChanged;
	
	/**
	 * PHP4 type constructor
	 */
	function RO_ChangeTrackingContainer($name, $defaultValue = '')
	{
		$this->__construct($name, $defaultValue);
	}
	
	
	/**
	 * PHP5 type constructor
	 */
	function __construct($name, $defaultValue = '')
	{	
		parent::__construct($name, $defaultValue);
	}
	
	/**
	 * Concrete implementation of the accept() method. Calls visitImageSize() on 
	 * the supplied visitor object.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		if(method_exists($visitor, 'visitCTContainerBefore'))
			$visitor->visitCTContainerBefore($this);
		parent::accept($visitor);
		if(method_exists($visitor, 'visitCTContainerAfter'))
			$visitor->visitCTContainerAfter($this);
	}
	
	/**
	 * Store old values before updating such that we can later check whether any of them changed.
	 *
	 */
	function storeOldValues()
	{
		$this->_oldValues = array();
		
		//foreach ($this->_children as $child){
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			$this->_oldValues[$child->getName()] = $child->store();
		}
	}
	
	/**
	 * Assess whether this option changed in the last update. Called from UpdateVisitor.
	 *
	 */
	function checkIfChanged()
	{
		$newValues = array();
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			$newValues[$child->getName()] = $child->store();
		}
		$this->_hasChanged = ($newValues !== $this->_oldValues);
	}
	
	/**
	 * Check whether this option changed in the last update.
	 *
	 * @return boolean
	 */
	function hasChanged()
	{
		return $this->_hasChanged;
	}

}

?>