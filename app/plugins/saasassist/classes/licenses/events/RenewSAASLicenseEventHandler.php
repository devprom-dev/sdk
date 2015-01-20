<?php

include_once SERVER_ROOT_PATH."core/classes/model/events/SystemTriggersBase.php";

class RenewSAASLicenseEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $object_it->object->getEntityRefName() != 'cms_License' ) return;
		
		unlink(SERVER_ROOT_PATH.'conf/license.dat');

	    getCheckpointFactory()->getCheckpoint('CheckpointSystem')->checkOnly( 
	    		array (
				    'LicenseSAASExpirationCheckpoint'
	    		)
	    );
	}
}
 