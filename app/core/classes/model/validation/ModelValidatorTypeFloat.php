<?php
include_once "ModelValidatorType.php";

class ModelValidatorTypeFloat extends ModelValidatorType
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('float'));
	}
	
	public function validate( & $value, array $groups = array() )
	{
        if ( in_array($value, array('','NULL')) ) return true;

        if ( count(array_intersect(array('hours','astronomic-time','working-time','daily-hours'), $groups)) > 0 ) {
            $value = SystemDateTime::parseHours($value);
            if ( $value < 0 ) return false;

            $maxValue = defined('MAX_DAILY_HOURS') ? MAX_DAILY_HOURS : 24;
            if ( in_array('daily-hours', $groups) && $value > $maxValue ) return false;

            if ( $value > 1000000 ) return false;
        }

		$value = str_replace(',', '.', $value);
		if( !is_numeric($value) ) return false;

		return true;
	}
}