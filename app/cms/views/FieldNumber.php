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
		if ( $this->readOnly() )
		{
		    echo '<span id="'.$this->getId().'" class="input-block-level well well-text" style="width:150px;">';
		        echo $this->getValue();
		    echo '</span>';
			
			echo '<input name="'.$this->getName().'" type="hidden" value="'.$this->getValue().'">';
		}
		else
		{
		?>
			<input class="input-medium" type="text" id="<? echo $this->getId() ?>" name="<? echo $this->getName(); ?>" value="<? echo $this->getValue(); ?>" tabindex="<? echo $this->getTabIndex() ?>" <?=($this->getRequired() ? 'required' : '')?> default="<?=$this->getDefault()?>" >
		<?
		}
	}
}
