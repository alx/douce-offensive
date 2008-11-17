<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The UpdateOptionVisitor:: is responsible for updating visited options. It 
 * typically visits objects after form submission.
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class UpdateOptionVisitor extends OptionVisitor
{
	
	/**
	 * Abstract implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitTextField(&$textField)
	 {
	 	if(isset($_POST[$textField->getName()]))
	 		$textField->setValue(attribute_escape($_POST[$textField->getName()]));
	 }
	 
	 
	 function visitStrictValidationTextField(&$textField)
	 {
	 	$oldValue = $textField->getValue();
	 	$this->visitTextField($textField);
	 	//check whether we pass validation if not put back the old value
	 	$errMsgs = $textField->validate();
	 	if(!empty($errMsgs))
	 		$textField->setValue($oldValue);	
	 }
	
	/**
	 * Abstract implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitPasswordTextField(&$textField)
	 {
	 	$this->visitTextField($textField);
	 }
	 
	 /**
	 * Abstract implementation of the visitTextArea() method called whenever a
	 * TextAreaOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextAreaOption &$textArea	Reference to visited option.
	 */
	 function visitTextArea(&$textArea)
	 {
	 	if(isset($_POST[$textArea->getName()]))
	 		$textArea->setValue(attribute_escape($_POST[$textArea->getName()]));
	 }
	 
	 /**
	 * Abstract implementation of the visitHiddenInputField() method called whenever a
	 * HiddenInputField is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object HiddenInputField &$hiddenInputField	Reference to visited option.
	 */
	 function visitHiddenInputField(&$hiddenInputField)
	 {
	 	$hiddenInputField->setValue(attribute_escape($_POST[$hiddenInputField->getName()]));
	 }
	
	/**
	 * Abstract implementation of the visitCheckBox() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxOption &$checkBox	Reference to visited option.
	 */
	 function visitCheckBox(&$checkBox)
	 {
	 	$checkBox->setValue(isset($_POST[$checkBox->getName()]) ? '1' : '0');
	 }

	/**
	 * Abstract implementation of the visitCheckBox() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxOption &$checkBox	Reference to visited option.
	 */
	 function visitRadioButtonListBefore(&$radioButtonList)
	 {	
	 	$radioButtonList->setValue($_POST[$radioButtonList->getName()]);
	 }
	 
	 function visitCheckBoxList(&$checkBoxList)
	 {	
	 	$checkBoxList->setValue(isset($_POST[$checkBoxList->getName()]) ? $_POST[$checkBoxList->getName()] : NULL);
	 }
	 
	 /**
	 * Abstract implementation of the visitDropDownListBefore() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object DropDownList &$dropDownList	Reference to visited option.
	 */
	 function visitDropDownListBefore(&$dropDownList)
	 {	
	 	$dropDownList->setValue($_POST[$dropDownList->getName()]);
	 }
	 
	/**
	 * Method called whenever a RO_ChangeTrackingContainer is visited. 
	 *
	 * @param object RO_ChangeTrackingContainer &$ctContainer	Reference to visited option.
	 */
	 function visitCTContainerBefore(&$ctContainer)
	 {
	 	$ctContainer->storeOldValues();
	 }
	 
	 /**
	 * Method called whenever a
	 * RO_ChangeTrackingContainer is visited.
	 *
	 * @param object RO_ChangeTrackingContainer &$ctContainer	Reference to visited option.
	 */
	 function visitCTContainerAfter(&$ctContainer)
	 {
	 	$ctContainer->checkIfChanged();
	 }
	 
	 

}

?>