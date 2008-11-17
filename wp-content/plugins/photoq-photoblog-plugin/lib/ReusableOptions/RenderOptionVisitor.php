<?php
/**
 * @package ReusableOptions
 */
 

/**
 * The RenderOptionVisitor:: is responsible for rendering of the options. It 
 * renders every visited option in HTML.
 *
 * @author  M. Flury
 * @package ReusableOptions
 */
class RenderOptionVisitor extends OptionVisitor
{
	
	/**
	 * Concrete implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitTextField(&$textField)
	 {
	 	$text = $textField->getTextBefore();
	 	if($textField->getLabel()){
	 		$text .= '<label for="'.$textField->getName().'">';
	 		$text .= $textField->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= ' <input type="text" name="'.$textField->getName().'" id="'.$textField->getName().'" ';
		$text .= 'size="'.$textField->getSize().'" maxlength="'.$textField->getMaxLength().'" ';
		$text .= 'value="'.stripslashes($textField->getValue()).'" /> ';
	 	$text .= $textField->getTextAfter();
	 	print $text;
	 }
	 
	 function visitStrictValidationTextField(&$textField)
	 {
	 	$this->visitTextField($textField);	
	 }
	 
	 /**
	 * Concrete implementation of the visitTextField() method called whenever a
	 * TextFieldOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextFieldOption &$textField	Reference to visited option.
	 */
	 function visitPasswordTextField(&$textField)
	 {
	 	$text = $textField->getTextBefore();
	 	if($textField->getLabel()){
	 		$text .= '<label for="'.$textField->getName().'">';
	 		$text .= $textField->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= ' <input type="password" name="'.$textField->getName().'" id="'.$textField->getName().'" ';
		$text .= 'size="'.$textField->getSize().'" maxlength="'.$textField->getMaxLength().'" ';
		$text .= 'value="'.stripslashes($textField->getValue()).'" /> ';
	 	$text .= $textField->getTextAfter();
	 	print $text;
	 }
	 
	/**
	 * Concrete implementation of the visitTextArea() method called whenever a
	 * TextAreaOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object TextAreaOption &$textArea	Reference to visited option.
	 */
	 function visitTextArea(&$textArea)
	 {
	 	$text = $textArea->getTextBefore();
	 	if($textArea->getLabel()){
	 		$text .= '<label for="'.$textArea->getName().'">';
	 		$text .= $textArea->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= ' <textarea name="'.$textArea->getName().'" id="'.$textArea->getName().'" ';
		$text .= 'rows="'.$textArea->getRows().'" cols="'.$textArea->getCols().'">';
		$text .= stripslashes($textArea->getValue()).'</textarea> ';
	 	$text .= $textArea->getTextAfter();
	 	print $text;
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
	 	$text = '<input type="hidden" name="'.$hiddenInputField->getName().'" id="'.$hiddenInputField->getName().'" ';
		$text .= 'value="'.stripslashes($hiddenInputField->getValue()).'" /> ';
	 	print $text;
	 }
	 
	 
	 /**
	 * Concrete implementation of the visitCheckBox() method called whenever a
	 * CheckBoxOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxOption &$checkBox	Reference to visited option.
	 */
	 function visitCheckBox(&$checkBox)
	 {
	 	$text = $checkBox->getTextBefore();
	 	if($checkBox->getLabel()){
	 		$text .= '<label for="'.$checkBox->getName().'">';
	 	}
	 	$text .= ' <input type="checkbox" name="'.$checkBox->getName().'" id="'.$checkBox->getName().'" ';
	 	if($checkBox->getValue())
	 		$text .= 'checked="checked"';
	 	$text .= ' /> ';
	 	if($checkBox->getLabel()){
	 		$text .= $checkBox->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= $checkBox->getTextAfter(); 
	 	
	 	print $text;
	 }

 	/**
	 * Concrete implementation of the visitRadioButton() method called whenever a
	 * RadioButtonOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object RadioButtonOption &$radioButton	Reference to visited option.
	 */
	 function visitRadioButton(&$radioButton)
	 {
	 	$text = $radioButton->getTextBefore();
	 	if($radioButton->getLabel()){
	 		$text .= '<label>';
	 	}
	 	$text .= ' <input type="radio" name="'.$radioButton->getName().'" ';
	 	$text .= 'value="'.$radioButton->getValue().'" ';
	 	if($radioButton->isSelected()){
	 		$text .= 'checked="checked"';
	 	}
	 	$text .= ' /> ';
	 	if($radioButton->getLabel()){
	 		$text .= $radioButton->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= $radioButton->getTextAfter(); 
	 	
	 	print $text;
	 }
	 
	/**
	 * Concrete implementation of the visitCheckBoxListOption() method called whenever a
	 * CheckBoxListOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object CheckBoxListOption &$checkBox	Reference to visited option.
	 */
	 function visitCheckBoxListOption(&$checkBox)
	 {
	 	$text = $checkBox->getTextBefore();
	 	if($checkBox->getLabel()){
	 		$text .= '<label>';
	 	}
	 	$text .= ' <input type="checkbox" name="'.$checkBox->getName().'[]" ';
	 	$text .= 'value="'.$checkBox->getValue().'" ';
	 	if($checkBox->isSelected()){
	 		$text .= 'checked="checked"';
	 	}
	 	$text .= ' /> ';
	 	if($checkBox->getLabel()){
	 		$text .= $checkBox->getLabel();
	 		$text .= '</label>';
	 	}
	 	$text .= $checkBox->getTextAfter(); 
	 	
	 	print $text;
	 }
	 
	  /**
	 * Concrete implementation of the visitDropDownListBefore() method called whenever a
	 * DropDownList is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object DropDownList &$dropDownList	Reference to visited option.
	 */
	 function visitDropDownListBefore(&$dropDownList)
	 {
	 	print $dropDownList->getTextBefore() . '<select name="'.$dropDownList->getName().'" id="'.$dropDownList->getName().'">';
	 }
	 
	 /**
	 * Concrete implementation of the visitDropDownListAfter() method called whenever a
	 * DropDownList is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object DropDownList &$dropDownList	Reference to visited option.
	 */
	 function visitDropDownListAfter(&$dropDownList)
	 {
	 	print "</select>";
	 	if($dropDownList->getLabel()){
	 		$text .= ' <label for="'.$dropDownList->getName().'">';
	 		$text .= $dropDownList->getLabel();
	 		$text .= '</label>';
	 		print $text;
	 	}
	 	print $dropDownList->getTextAfter();
	 }
	 
	 
	/**
	 * Concrete implementation of the visitRadioButtonListBefore() method called whenever a
	 * RadioButtonList is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object RadioButtonList &$radioButtonList	Reference to visited option.
	 */
	 function visitRadioButtonListBefore(&$radioButtonList)
	 {
	 	print $radioButtonList->getTextBefore();
	 }
	 
	 /**
	 * Concrete implementation of the visitRadioButtonListAfter() method called whenever a
	 * RadioButtonList is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object RadioButtonList &$radioButtonList	Reference to visited option.
	 */
	 function visitRadioButtonListAfter(&$radioButtonList)
	 {
	 	if($radioButtonList->getLabel()){
	 		$text .= ' <label for="'.$radioButtonList->getName().'">';
	 		$text .= $radioButtonList->getLabel();
	 		$text .= '</label>';
	 		print $text;
	 	}
	 	print $radioButtonList->getTextAfter();
	 }
	 
	 /**
	 * Concrete implementation of the visitDropDownOption() method called whenever a
	 * DropDownOption is visited. Subclasses should override this and and
	 * define the operation to be performed.
	 *
	 * @param object DropDownOption &$dropDownOption	Reference to visited option.
	 */
	 function visitDropDownOption(&$dropDownOption)
	 {
	 	$text = '';
	 	$text .= '<option value="'.$dropDownOption->getValue().'"';
	 	if($dropDownOption->isSelected()){
	 		$text .= ' selected="selected"';
	 	}
	 	$text .= '>';
	 	$text .= $dropDownOption->getName().'</option>';
	 	
	 	print $text;
	 }
	
}

?>