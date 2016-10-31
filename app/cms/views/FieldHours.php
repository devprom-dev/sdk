<?php

class FieldHours extends Field
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
		    echo '<span id="'.$this->getId().'" class="input-block-level well well-text" style="width:150px;">';
		        echo getSession()->getLanguage()->getDurationWording($this->getValue(), 8);
		    echo '</span>';
			echo '<input name="'.$this->getName().'" type="hidden" value="'.$this->getValue().'">';
		}
		else {
		    echo '<span class="pull-left" style="min-width:210px;">';
                echo '<input class="pull-left input-small" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' default="'.$this->getDefault().'">';
                echo '<div class="pull-left" style="margin-top:4px;margin-left:6px;">'.text(2126).'</div>';
            echo '</span>';
		}
	}
}
