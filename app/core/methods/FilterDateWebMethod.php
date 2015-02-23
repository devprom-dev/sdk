<?php

include_once "SelectDateRefreshWebMethod.php";

class FilterDateWebMethod extends SelectDateRefreshWebMethod
{
 	var $method_name;
 	
 	function FilterDateWebMethod ( $table_name = '' )
 	{
 		$this->method_name = md5($table_name.$this->getMethodName());
 		parent::__construct();
 	}

 	function hasAccess()
 	{
 		return true;
 	}

 	function getStyle()
 	{
 		return 'width:120px;';
 	}

  	function drawSelect( $parms_array = array() ) 
 	{
 		return $this->draw( $parms_array );
 	}
 	
 	function draw( $parms_array = array() ) 
 	{
 		parent::draw( 
 			array_merge($parms_array, array('setting' => $this->method_name )),
 			$this->getValue()
 		);
 	}

 	function execute_request()
 	{
 		if ( $this->getValueParm() != '' )
 		{
 			echo $this->getValueParm().'='.trim($_REQUEST['value']);
 		}
 	}
}