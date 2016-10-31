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
		if ( strpos($value, '0000-00-00') !== false ) return '';
		if ( strtolower($value) == 'now()' ) return $value;

		$db_date = getLanguage()->getDbDate(trim($value, "'"));
		if ( $db_date == '' ) return '';

		return $db_date;
	}
}
