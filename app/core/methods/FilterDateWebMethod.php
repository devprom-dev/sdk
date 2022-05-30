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

 	function hasAccess() {
 		return true;
 	}

 	function getStyle() {
 		return 'width:120px;';
 	}

  	function drawSelect( $parms_array = array() ) {
 		return $this->draw( $parms_array );
 	}
 	
 	function draw( $parms_array = array(), $default_value = '' )
 	{
 		parent::draw( 
 			array_merge($parms_array, array('setting' => $this->method_name )),
 			$this->getValue()
 		);
 	}

 	function parseFilterValue($value, $context)
    {
        return parent::parseFilterValue(
            SystemDateTime::parseRelativeDateTime($value, getSession()->getLanguage()), $context
        );
    }
}