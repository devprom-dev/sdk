<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingDateTime extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('datetime'));
	}
	
	public function map( $value )
	{
		if ( $value == '' ) return '';
		
		if ( strtolower($value) == 'now()' ) return $value;

		$value = trim($value, "'");
		
		list($date_value, $time_value) = preg_split('/\s+/', $value);

		$db_date = getLanguage()->getDbDate($date_value);
		
		if ( $db_date != '' ) return SystemDateTime::convertToServerTime($db_date." ".$time_value);
		
		return $date_value." ".$time_value;
	}
}