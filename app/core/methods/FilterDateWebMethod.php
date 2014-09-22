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
 			array('setting' => $this->method_name ), $this->getValue() );
 	}

 	function execute_request()
 	{
 		global $_REQUEST;
 		
 		if ( $_REQUEST['value'] == '' ) $_REQUEST['value'] = 'all';
 		
 		$this->execute($_REQUEST['setting'], $_REQUEST['value']);

 		if ( $this->getValueParm() != '' )
 		{
 			echo $this->getValueParm().'='.trim($_REQUEST['value']);
 		}
 	}

 	function execute ( $method, $value )
 	{
 	}
}