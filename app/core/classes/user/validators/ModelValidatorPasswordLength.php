<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorPasswordLength extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms['Password'] == '' ) return "";

		return strlen(trim($parms['Password'])) < EnvironmentSettings::getPasswordLength()
			? preg_replace('/%1/', EnvironmentSettings::getPasswordLength(), text(2072)) : "";
	}
}