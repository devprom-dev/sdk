<?php

class FieldPassword extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
      
 	function draw( $view = null )
	{
		if ( $this->readOnly() )
		{
			echo '<input class="input-block-level" type="text" value="'.$this->getValue().'" disabled>';
		}
		else
		{
		?>
    	    <input class="input-block-level" type="password" name="<? echo $this->getName(); ?>" id="<? echo $this->getId(); ?>" tabindex="<? echo $this->getTabIndex() ?>" value="<? echo ($this->getValue() != '' ? SHADOW_PASS : '') ?>" <?=($this->getRequired() ? 'required' : '')?> autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">
		<?
		}
	}
}