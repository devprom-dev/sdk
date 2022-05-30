<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorWatcherSubject extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
	    if ( array_key_exists('Email', $parms) || array_key_exists('SystemUser', $parms) ) {
            if ( $parms['Email'] == '' && $parms['SystemUser'] == '' ) return text(3117);
        }
	    return "";
	}
}