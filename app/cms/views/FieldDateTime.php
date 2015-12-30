<?php

class FieldDateTime extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
    
	function getText()
	{
		return getSession()->getLanguage()->getDateFormatted($this->getValue());
	}
    
	function setValue( $value )
	{
		if ( preg_match('/([0-9]+\-)+/', $value) > 0 )
		{
			$value = getSession()->getLanguage()->getDateFormatted($value);
		}		
		
		parent::setValue( $value );
	}
	
    function draw()
	{
		if ( $this->readOnly() )
		{
			echo '<input class="input-medium" type="text" tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' readonly>';
		}
		else
		{
			echo '<input class="datepicker input-medium" type="text" tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' >';
		}
	}
}