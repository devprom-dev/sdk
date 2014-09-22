<?php

include_once "AutocompleteWebMethod.php";

class ExecuteAutoCompleteWebMethod extends AutocompleteWebMethod
{
 	function ExecuteAutoCompleteWebMethod( $object = null, $title = '' )
 	{
 		parent::AutocompleteWebMethod( $object, $title );
 	}

 	function getStyle()
 	{
 		return 'width:180px;';
 	}
 	
 	function drawSelect( $parms_array = array(), $default_value ) 
 	{
 		global $script_number;
 		$script_number += 1;
 		
 		$parms_array['class'] = get_class($this->object);
 		$url = $this->getUrl( $parms_array );
 		
 		if ( $default_value != '' )
 		{
 			$value_it = $this->object->getRegistry()->Query(
 					array (
 							is_numeric($default_value)
 									? new FilterInPredicate($default_value)
 									: new FilterAttributePredicate('Caption', $default_value)
 					)
 			);

 			$value = $value_it->getDisplayName();
 		}

	 	echo '<input id="filter_'.$this->getMethodName().'" class="input" type="text" value="'.$value.'" title="'.$this->getTitle().'">';
	 		
	 	echo '<script type="text/javascript">$(document).ready(function(){ executeAutoComplete("'.
	 		$this->getMethodName().'", "'.$url.'", "'.$this->title.'"); });</script>';
 	}
 	
 	function execute_request()
 	{
 		global $_REQUEST;
 		
 		if ( $_REQUEST['value'] != '' )
 		{
 			$this->execute( $_REQUEST, $_REQUEST['value'] );
 		}
 		else
 		{
 			parent::execute_request();
 		}
 	}

 	function execute( $parms, $value )
 	{
 	}
}