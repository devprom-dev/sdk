<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingFloat extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('float'));
	}
	
	public function map( $value )
	{
		if ( $value == '' ) return '';

		$match = array();
		if ( preg_match(SystemDateTime::getTimeParseRegex(), $value, $match) and count($match) > 1 ) {
			$value = 0;
			$value += $match[2] * 8 * 60;
			$value += $match[4] * 60;
			$value += $match[6];
			return round($value / 60, 4);
		}

		$value = str_replace(',', '.', $value);
		if ( !is_numeric($value) ) return '0';
	
		return $value;
	}
}