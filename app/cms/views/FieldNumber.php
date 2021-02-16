<?php

class FieldNumber extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
     
    function getText()
    {
    	return number_format(floatval($this->getValue()), 0, ',', ' ');
	}
      
 	function draw( $view = null )
	{
		if ( $this->readOnly() ) {
			echo '<input class="input-medium" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" readonly>';
		}
		else
		{
		?>
			<input class="input-medium" type="text" id="<? echo $this->getId() ?>" name="<? echo $this->getName(); ?>" value="<? echo $this->getValue(); ?>" tabindex="<? echo $this->getTabIndex() ?>" <?=($this->getRequired() ? 'required' : '')?> default="<?=$this->getDefault()?>" >
		<?
		}
	}
}
