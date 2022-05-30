<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingWYSIWYG extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('wysiwyg'));
	}
	
	public function map( $value, array $groups = array() ) {
		return trim($value, " \r\n");
	}
}
