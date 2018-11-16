<?php

class FieldDateTime extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
    
	function setValue( $value )
	{
		parent::setValue(
            str_replace('00:00', '',
                getSession()->getLanguage()->getDateTimeFormatted(
                    SystemDateTime::convertToClientTime($value)
                )
            )
        );
	}

    function draw( $view = null )
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