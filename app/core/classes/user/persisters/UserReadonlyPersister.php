<?php

class UserReadonlyPersister extends ObjectSQLPersister
{
 	function map( &$parms )
	{
		if ( $parms[$this->getObject()->getIdAttribute()] > 0 ) return;

		// check license restrictions when user is to be created
		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
		if ( !$license_it->allowCreate($this->getObject()) ) {
			$parms['IsReadonly'] = 'Y';
		}
	}
}
