<?php

include_once "SelectDateRefreshWebMethod.php";

class FilterDateWebMethod extends SelectDateRefreshWebMethod
{
 	function FilterDateWebMethod ( $title = '', $parm = '' )
 	{
 		$this->setCaption($title);
        $this->setValueParm($parm);
 		parent::__construct();
 	}

 	function setDefault( $value )
 	{
 		$this->default_value = $value;
 	}
 	
 	function getDefault()
 	{
 		return $this->default_value;
 	}

 	function getValue()
 	{
 		$value = parent::getValue();
		if ( $value == 'all' ) return '';
 		return $value != '' ? $value : $this->default_value;
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
}