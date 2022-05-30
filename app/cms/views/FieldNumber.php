<?php

class FieldNumber extends Field
{
    private $decimals = 0;
    private $dimension = '';

    function readOnly() {
         return !$this->getEditMode() || parent::readOnly();
    }

    function setDecimals( $value ) {
        $this->decimals = $value;
    }

    function setDimension( $value ) {
        $this->dimension = $value;
    }

    function getText() {
        return number_format(floatval(parent::getText()), $this->decimals, ',', ' ') . $this->dimension;
    }

    function draw( $view = null )
	{
		if ( $this->readOnly() ) {
            $readonlyValue = number_format(floatval($this->getValue()), $this->decimals, ',', ' ');
			echo '<input class="input-medium" type="text" id="'.$this->getId().'" name="'.$this->getName().'" value="'.$readonlyValue.'" readonly>';
		}
		else
		{
            echo '<span class="pull-left" style="min-width:210px;">';
                echo '<input class="pull-left input-small" type="text" id="'.$this->getId().'" autocomplete="off" name="'.$this->getName().'" value="'.$this->getValue().'" tabindex="'.$this->getTabIndex().'" '.($this->getRequired() ? 'required' : '').' default="'.$this->getDefault().'">';
                echo '<div class="pull-left" style="margin-top:4px;margin-left:6px;">'.$this->dimension.'</div>';
            echo '</span>';
            echo '<div class="clearfix"></div>';
		}
	}
}
