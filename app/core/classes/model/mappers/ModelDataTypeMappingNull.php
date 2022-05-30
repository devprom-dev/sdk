<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingNull extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return true;
	}
	
	public function map( $value, array $groups = array() ) {
		return $value;
	}
}
