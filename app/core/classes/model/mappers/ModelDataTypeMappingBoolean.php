<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingBoolean extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('char'));
	}
	
	public function map( $value )
	{
		return (in_array($value, array('','on','Y')) ? 'Y' : 'N');
	}
}
