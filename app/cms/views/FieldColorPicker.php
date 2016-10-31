<?php

class FieldColorPicker extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
    
    function draw( $view = null )
	{
		if ( $this->readOnly() )
		{
			echo '<div class="colorPicker-picker"></div>';
		}
		else
		{
			echo '<input tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'">';
			echo '<script type="text/javascript"> $(document).ready(function() { $("#'.$this->getId().'").colorPicker({pickerDefault:"'.$this->getValue().'"}); }); </script>';
		}
	}
}