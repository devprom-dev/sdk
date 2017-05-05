<?php

class FieldHours extends Field
{
    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
     
	function getValue()
    {
        if ( parent::getValue() == '' ) return '';
        $value = parent::getValue();
        return ($value < 0 ? '-' : '') . getSession()->getLanguage()->getDurationWording(abs($value), 24);
    }

    function draw( $view = null )
	{
		if ( $this->readOnly() ) {
		    echo '<span id="'.$this->getId().'" class="input-block-level well well-text" style="width:90px;min-width:90px;">';
		        echo $this->getValue();
		    echo '</span>';
			echo '<input name="'.$this->getName().'" type="hidden" value="'.$this->getValue().'">';
		}
		else {
		    echo '<span class="pull-left" style="min-width:210px;">';
                echo '<input class="pull-left input-small" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.trim($this->getValue(), 'ч').'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' default="'.$this->getDefault().'">';
                echo '<div class="pull-left" style="margin-top:4px;margin-left:6px;">'.text(2126).'</div>';
            echo '</span>';
            echo '<div class="clearfix"></div>';
		}
	}
}
