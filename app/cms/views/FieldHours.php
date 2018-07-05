<?php

class FieldHours extends Field
{
    const HOURS_CALENDAR = 24;
    const HOURS_WORKING = 8;

    function __construct( $hoursMode = self::HOURS_CALENDAR )
    {
        parent::__construct();
        $this->hoursMode = $hoursMode;
    }

    function readOnly()
    {
         return !$this->getEditMode() || parent::readOnly();
    }
     
	function getValue()
    {
        if ( parent::getValue() == '' ) return '';
        $value = parent::getValue();
        return ($value < 0 ? '-' : '') . getSession()->getLanguage()->getDurationWording(abs($value), $this->hoursMode);
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
                echo '<input class="pull-left input-small" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.parent::getValue().'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' default="'.$this->getDefault().'">';
                echo '<div class="pull-left" style="margin-top:4px;margin-left:6px;">'.text(2126).'</div>';
            echo '</span>';
            echo '<div class="clearfix"></div>';
		}
	}

    private $hoursMode;
}
