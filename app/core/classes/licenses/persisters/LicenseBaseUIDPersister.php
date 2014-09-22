<?php

class LicenseBaseUIDPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( "(SELECT '".INSTALLATION_UID."') InstallationUID " );
 	}
}
