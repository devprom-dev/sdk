<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingFloat extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('float'));
	}
	
	public function map( $value, array $groups = array() )
	{
		if ( $value == '' ) return '';

        if ( count(array_intersect(array('hours','astronomic-time','working-time','daily-hours'), $groups)) > 0 ) {
            return SystemDateTime::parseHours($value);
        }

		$value = str_replace(',', '.', $value);
		if ( !is_numeric($value) ) return '0';
	
		return $value;
	}
}