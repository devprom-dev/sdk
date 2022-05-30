<?php
include_once "WebMethod.php";

class SelectDateRefreshWebMethod extends WebMethod
{
 	function SelectDateRefreshWebMethod()
 	{
 		parent::WebMethod();
 	}
 	
 	function draw( $parms_array, $default_value ) 
 	{
 		global $script_number;
 		$url = $this->getUrl( $parms_array );
 		
 		$script_number += 1;
 		
 		$value = $this->getValue();
 		if ( in_array($value, array('','all')) ) $value = $default_value; 
 		
 		$this->id = 'dt'.$script_number;
 		
		$container = 'select_'.$this->id.'Container';
		$call_var = 'select_'.$this->id.'Var';

		if ( in_array($value, array('','all','hide'))) {
            $value = '';
        }
		if ( preg_match('/([0-9]+\-)+/', $value) > 0 )
		{
			$value = getSession()->getLanguage()->getDateFormatted($value);
		}

		echo ' <input type="text" class="btn-sm input-small datepicker-filter" title="'.$this->getCaption().'" style="'.$this->getStyle().'" id="select_'.$this->id.'" value="'.$value.'" placeholder="'.$this->getCaption().'" valueparm="'.$this->getValueParm().'">';
 	}
 	
 	function getStyle()
 	{
 		return 'margin-left:1pt;padding-left:1pt;margin-top:2pt;';
 	}

 	function getValue() {
        return SystemDateTime::parseRelativeDateTime(parent::getValue(), getLanguage());
    }
}
