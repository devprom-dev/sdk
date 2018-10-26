<?php

class FieldShortText extends Field
{
      function readOnly()
     {
         return !$this->getEditMode() || parent::readOnly();
     }

    function draw(  $view = null  )
	{
		if ( $this->readOnly() )
		{
			echo '<div class="input-block-level well well-text">';
				echo IteratorBase::getHtmlValue($this->getValue());
			echo '</div>';
			echo '<input id="'.$this->getId().'" type="hidden" name="'.$this->getName().'" value="'.$this->getValue().'">';
		}
		else
		{
			echo '<input class="input-block-level" type="text" id="'.$this->getId().'" name="'.$this->getName().'" tabindex="'.$this->getTabIndex().'" value="'.$this->getEncodedValue().'" '.($this->getRequired() ? 'required' : '').' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="'.htmlentities($this->getDefault()).'">';
		}
	}
}