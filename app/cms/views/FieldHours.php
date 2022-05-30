<?php

class FieldHours extends Field
{
    const HOURS_CALENDAR = 24;
    const HOURS_WORKING = 8;
    const HOURS_ONLY = 0;

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
        return ($value < 0 ? '-' : '') .
            ($this->hoursMode == self::HOURS_ONLY
                ? getSession()->getLanguage()->getHoursWording(abs($value))
                : getSession()->getLanguage()->getDurationWording(abs($value), $this->hoursMode)
            );
    }

    function draw( $view = null )
	{
		if ( $this->readOnly() ) {
            echo '<input class="input-medium" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$this->getValue().'" readonly>';
		}
		else {
		    echo '<span class="pull-left" style="min-width:210px;">';
                echo '<input class="pull-left input-small" type="text" id="'.$this->getId().'" autocomplete="off" name="'.$this->getName().'" value="'.parent::getValue().'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' default="'.$this->getDefault().'">';
                echo '<div class="pull-left" style="margin-top:4px;margin-left:6px;">'.text(2126).'</div>';
            echo '</span>';
            echo '<div class="clearfix"></div>';
		}
	}

    private $hoursMode;
}
