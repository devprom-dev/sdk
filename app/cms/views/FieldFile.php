<?php

class FieldFile extends Field
{
 	function draw()
	{
		echo '<input id="'.$this->getId().'" tabindex="'.$this->getTabIndex().'" name="'.$this->getName().'" value="'.$this->getValue().'" type="file" '.($this->readOnly() ? 'readonly' : '').' >';
	}
} 
