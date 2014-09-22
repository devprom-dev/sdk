<?php

class FieldStatic extends Field
{
 	var $overriden_value;
 	
 	function FieldStatic( $value = '' ) 
 	{
 		$this->overriden_value = $value;
 		parent::Field(); 
 	}
 	
 	function draw()
	{
		echo '<div class="input-border">';
		?>
        <input id="<? echo $this->getId() ?>" type="hidden" name="<? echo $this->getName(); ?>" value="<? echo $this->getEncodedValue(); ?>">
        <input style="width:100%;" disabled name="<? echo $this->getName().'Static'; ?>" value="<? echo ($this->overriden_value != '' ? $this->overriden_value : $this->getEncodedValue()); ?>">
		<?
		echo '</div>';
	}
}
