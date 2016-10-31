<?php

class FieldFile extends Field
{
 	function draw( $view = null )
	{
		echo '<div><input id="'.$this->getId().'" tabindex="'.$this->getTabIndex().'" name="'.$this->getName().'" value="'.$this->getValue().'" type="file" '.($this->readOnly() ? 'readonly' : '').' ></div>';
	}
	
	function getValidator()
	{
		return new ModelValidatorTypeFile();
	}
} 
