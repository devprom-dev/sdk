<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingDate extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('date'));
	}
	
	public function map( $value )
	{
	    list($date, $time) = preg_split('/\s+/', $value);
		if ( $date == '' ) return '';

        $value = SystemDateTime::convertToServerTime(
            SystemDateTime::parseRelativeDateTime($date, getLanguage()) . date(' H:i:s')
        );

        if ( $value == '' ) return '';

		return $value;
	}
}
