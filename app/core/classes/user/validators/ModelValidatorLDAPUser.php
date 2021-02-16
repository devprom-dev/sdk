<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorLDAPUser extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms['LDAPUID'] == '' ) return "";

        $object->setAttributeRequired('Password', false);
        $parms['RepeatPassword'] = $parms['Password'] = 'NULL';

		return "";
	}
}