<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingDate extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('date'));
	}
	
	public function map( $value )
	{
	    list($date, $time) = preg_split('/\s+/', $value);
		if ( $date == '' ) return '';

        $value = SystemDateTime::parseRelativeDateTime($date, getLanguage());
        if ( $value == '' ) return '';

        try {
            $time = new DateTime($date, new DateTimeZone("UTC"));
            return $time->format("Y-m-d");
        }
        catch( Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
        }

        return "";
	}
}
