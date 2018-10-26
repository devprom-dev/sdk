<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorSystemSettingsTimezone extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
        if ( !preg_match('/[+|-]\d{2,}/', $parms['TimeZoneUTC'], $matches ) ) {
            $parms['TimeZoneUTC'] = '+00';
        }
	}
}