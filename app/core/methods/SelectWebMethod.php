<?php

include_once "WebMethod.php";

class SelectWebMethod extends WebMethod
{
 	function SelectWebMethod()
 	{
 		parent::WebMethod();
 	}

 	function getValues() 
 	{
 		return array();
 	}
 	
 	function execute_request() 
 	{
 		global $_REQUEST;
 		$this->execute($_REQUEST, $_REQUEST['value']);
 	}
 	
 	function getStyle()
 	{
 		return 'width:100%;';
 	}
 	
 	function drawSelect( $parms_array = array(), $default_value  = '') 
 	{
 		if ( !$this->hasAccess() )
 		{
 			echo $default_value;
 		}
 		else
 		{
	 		global $script_number;
	 		$url = $this->getUrl( $parms_array );
	 		
	 		$script_number += 1;
	 		?>
		 	<select id=select_<? echo get_class($this).$script_number ?> onchange="javascript: runMethod('<? echo $url ?>', { 'value': $(this).val() }, 'donothing', '');" style="<? echo $this->getStyle() ?>" title="<? echo $this->getCaption() ?>">
		 	<?
		 		$values = $this->getValues();
		 		$keys = array_keys($values);
		 		$checked = '';
		 		$selected = false;

		 		for($i = 0; $i < count($keys); $i++) 
		 		{
		 			$checked = (trim($keys[$i]) == trim($default_value) ? 'selected' : '');
		 			
		 			if ( $checked != '' )
		 			{
		 				$selected = true;
		 			}
		 			
		 			echo '<option value="'.$keys[$i].'" '.$checked.' >'.$values[$keys[$i]].'</option>';
		 		}
		 		
		 		if ( !$selected )
		 		{
		 			echo '<option value="'.$default_value.'" selected >'.$default_value.'</option>';
		 		}
		 	?>
		 	</select>
			<?
 		}
    }
}