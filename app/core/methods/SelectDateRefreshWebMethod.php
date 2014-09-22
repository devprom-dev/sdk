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
 		
 		$this->value = $default_value;
 		if ( $this->value == 'all' )
 		{
 			$this->value = '';
 		}
 		
 		$this->id = 'dt'.$script_number;
 		
		$container = 'select_'.$this->id.'Container';
		$call_var = 'select_'.$this->id.'Var';
		
		if ( preg_match('/([0-9]+\-)+/', $this->value) > 0 )
		{
			$this->value = getSession()->getLanguage()->getDateFormatted($this->value);
		}

		echo ' <input type="text" class="btn-small input-small datepicker" title="'.$this->getCaption().'" style="'.$this->getStyle().'" id="select_'.$this->id.'" value="'.$this->value.'" placeholder="'.$this->getCaption().'" onchange="javascript: selectRefreshMethod(\''.$url.'\', \''.$this->id.'\')">';
 	}
 	
 	function getStyle()
 	{
 		return 'margin-left:1pt;padding-left:1pt;margin-top:2pt;';
 	}
}
