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
		if ( $value == '' ) return '';
		
		if (strtolower($value) == 'now()') return $value;

		$value = trim($value, "'");

		$db_date = getLanguage()->getDbDate($value);
		
		if ( $db_date == '' ) return $value;

		// date given by user is in server time already
		return $db_date;
	}
}
