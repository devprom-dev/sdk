<?php

include_once "SelectRefreshWebMethod.php";

class FilterWebMethod extends SelectRefreshWebMethod
{
 	var $method_name;
 	
 	protected $default_value = '';
 	
 	function FilterWebMethod ( $table_name = '' )
 	{
 		$this->method_name = md5($table_name.$this->getMethodName());
 		parent::SelectRefreshWebMethod();
 	}

	function setDefaultValue( $value )
	{
	    return $this->default_value = $value;
	}

	function getDefaultValue()
	{
		return $this->default_value;
	}
	
 	function hasAccess()
 	{
 		return true;
 	}

 	function getStyle()
 	{
 		return 'width:120px;';
 	}

 	function getClass()
 	{
 		return 'filter';
 	}
 	
 	function drawSelect( $parms_array = array() ) 
 	{
 		parent::drawSelect( 
 			array_merge( array('setting' => $this->method_name ), $parms_array ), 
 			$this->getValue() 
 		);
 	}
 	
 	function getName()
 	{
 		return $this->getValueParm();
 	}

 	function getValue()
 	{
 		$value = parent::getValue();

 		if ( $value != '' ) return $value;

		return $this->default_value;
 	}
 	
 	function execute_request()
 	{
 		if ( $this->getValueParm() != '' )
 		{
 			echo $this->getValueParm().'='.trim($_REQUEST['value']);
 		}
 	}
}