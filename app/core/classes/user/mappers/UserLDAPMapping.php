<?php

class UserLDAPMapping
{
	public function map( Metaobject $object, array & $parms )
	{
		if ( $parms['LDAPUID'] == '' ) return;
        $parms['RepeatPassword'] = $parms['Password'] = 'NULL';
	}
}