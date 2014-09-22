<?php

class FieldLargeText extends Field
{
	function __construct()
	{
		parent::__construct();
		
		$this->setRows(4);
	}
	
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }

 	function getRows()
 	{
 		return $this->rows_number;
 	}
 	
 	function setRows( $rows )
 	{
 		$this->rows_number = $rows;
 	}
 	
 	function draw()
	{
		echo '<textarea class="input-block-level" '.($this->Readonly() ? 'disabled' : '').' id="'.$this->getId().'" name="'.$this->getName().'" rows="'.$this->getRows().'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' >'.$this->getValue().'</textarea>';
	}
}
