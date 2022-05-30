<?php

class FieldShortText extends Field
{
     function readOnly() {
         return !$this->getEditMode() || parent::readOnly();
     }

    function draw(  $view = null  )
	{
		if ( $this->readOnly() ) {
			echo '<input id="'.$this->getId().'" type="text" class="input-block-level" name="'.$this->getName().'" value="'.$this->getValue().'" readonly>';
		}
		else {
			echo '<input class="input-block-level" autocomplete="off" type="text" id="'.$this->getId().'" name="'.$this->getName().'" tabindex="'.$this->getTabIndex().'" value="'.$this->getEncodedValue().'" '.($this->getRequired() ? 'required' : '').' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="'.htmlentities($this->getDefault()).'">';
		}
	}
}