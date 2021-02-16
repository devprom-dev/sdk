<?php
include_once "ModelValidatorType.php";

class ModelValidatorTypeReference extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return strpos($type_name, "ref_") !== false;
	}
	
	public function validate( & $value )
	{
        $ids = \TextUtils::parseItems($value);
		return $value == '' || is_array($ids);
	}
}