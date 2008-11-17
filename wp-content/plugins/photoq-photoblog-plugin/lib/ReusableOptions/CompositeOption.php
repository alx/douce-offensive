<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The CompositeOption:: is the parent class of all options that can contain. 
 * other options. Composite object of the composite pattern.
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class CompositeOption extends ReusableOption
{
	
	/**
	 * Any nested suboptions an option might have if it is composed of several
	 * primitive options.
	 *
	 * @var array object ReusableOption
	 * @access private
	 */
	var $_children;
	
	/**
	 * PHP4 type constructor
	 */
	function CompositeOption($name, $defaultValue, $label = '', 
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
		$this->_children = array();
	}
	
	/**
	 * Let's the visitor visit each of the children.
	 *
	 * @param object OptionVisitor &$visitor	Reference to visiting visitor.
	 */
	function accept(&$visitor)
	{
		//foreach ($this->_children as $child){
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			$child->accept($visitor);
		}
	}
	
	
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
			//pass it on to all the children to give them a chance to load
			foreach ( array_keys($this->_children) as $index ) {
				$child =& $this->_children[$index];
				if($child->getName() == $this->_name) 
					$child->load($storedOptions);			
				elseif(array_key_exists($child->getName(), $storedOptions))
					$child->load($storedOptions[$child->getName()]);
			}
		}	
		
	}
	
	/**
	 * Gets an array of options to be stored in the database. Recursively obtains
	 * options from children.
	 * 
	 * @return array $result		Array of options to store in database.
	 * @access public
	 */
	function store()
	{
		$result = array();
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			$result[$child->getName()] = $child->store();
		}
		return $result;
	}
	
	
	/**
	 * Add an option to the composite.	
	 * 
	 * @param object ReusableOption &$option  The option to be added to the composite.
	 * @return boolean	True if options could be added (composite), false otherwise.
	 * @access public
	 */
	function addChild(&$option)
	{	
		$this->_children[] = $option;
		return true;
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
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			if($child->getName() == $name){
				unset($this->_children[$index]);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Remove all options from the composite.	
	 * 
	 * @access public
	 */
	function removeChildren()
	{	
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			unset($this->_children[$index]);
		}
	}
	
	/**
	 * Recursively traverses this option and all its descendants to find an 
	 * option with a given name
	 *
	 * @param string $name				The name of the option we are looking for.
	 * @return object ReusableOption	The option if found, null otherwise.
	 * @access public
	 *
	 */
	function &getOptionByName($name){
		$option = null;
		if($this->_name == $name)
			$option =& $this;
		else
			foreach ( array_keys($this->_children) as $index ) {
				$child =& $this->_children[$index];
				$option =& $child->getOptionByName($name);
				if($option){
					break;
				}
			}
		
		return $option;
	}
	
	function countChildren()
	{
		return count($this->_children);
	}
	
	function &getChild($int)
	{
		$option = null;
		if($int >= 0 && $int < $this->countChildren())
			$option =& $this->_children[$int];
		return $option;
	}
	
	/**
	 * Validate the child options.
	 *
	 * @return array
	 */
	function validate()
	{
		$result = array();
		
		//foreach ($this->_children as $child){
		foreach ( array_keys($this->_children) as $index ) {
			$child =& $this->_children[$index];
			$result = array_merge($result, $child->validate());
		}
		return $result;
	}


}

?>