<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingFile extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('file','image'));
	}
	
	public function mapInstance( $attribute, $values, array $groups )
	{
		if ( in_array($values[$attribute], array('','file')) ) return $values[$attribute];
		
		$filename = tempnam(SERVER_UPDATE_PATH, 'tux');
		file_put_contents($filename, base64_decode($values[$attribute]));
		
		$_FILES[$attribute]['tmp_name'] = $filename;
		$_FILES[$attribute]['name'] = $values[$attribute.'Ext'];
		$_FILES[$attribute]['type'] = $values[$attribute.'Mime'];
		
		return "";
	}
	
	public function map( $value, array $groups = array() ) {}
}
