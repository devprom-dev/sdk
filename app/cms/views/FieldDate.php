<?php

class FieldDate extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
    
    function draw( $view = null )
	{
		if ( $this->readOnly() )
		{
			echo '<input class="input-medium" type="text" tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' readonly>';
		}
		else
		{
			echo '<input class="datepicker input-medium" type="text" autocomplete="off" tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' >';
		}
	}
}