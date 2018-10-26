<?php

class FieldCopyClipboard extends Field
{
    function draw( $view = null )
	{
		echo '<input class="input-block-level" type="text" tabindex="'.$this->getTabIndex().'" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getEncodedValue().'" '.($this->getRequired() ? 'required' : '').' readonly style="width:90%">';
		echo ' &nbsp; <a class="btn btn-sm btn-light clipboard" data-clipboard-text="'.$this->getEncodedValue().'" data-message="'.text(2107).'" tabindex="-1" title="'.text(2605).'" style="margin-top:1px;">';
		echo '<i class="icon-share"></i>';
        echo '</a>';
	}
}