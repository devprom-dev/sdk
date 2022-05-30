<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingString extends ModelDataTypeMapping
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('varchar','text','largetext'));
	}
	
	public function map( $value, array $groups = array() )
	{
        if ( is_array($value) ) $value = join(',', $value);
		return \TextUtils::stripAnyTags(trim(html_entity_decode($value), " \r\n"));
	}
}
