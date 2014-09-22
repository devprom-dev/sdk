<?php

class FieldShortText extends Field
{
      function readOnly()
     {
         return !$this->getEditMode() || parent::readOnly();
     }
     
    function draw()
	{
		if ( $this->readOnly() )
		{
			echo '<div class="input-block-level well well-text">';
				echo $this->getText();
			echo '</div>';
			echo '<input id="'.$this->getId().'" type="hidden" name="'.$this->getName().'" value="'.$this->getValue().'">';
		}
		else
		{
			echo '<input class="input-block-level" type="text" id="'.$this->getId().'" name="'.$this->getName().'" tabindex="'.$this->getTabIndex().'" value="'.$this->getValue().'" '.($this->getRequired() ? 'required' : '').' placeholder="'.$this->getDefault().'">';
		}
	}
}