<?php

include_once SERVER_ROOT_PATH."admin/classes/CheckpointRegistryBuilder.php";
include "licenses/LicenseSAASExpirationCheckpoint.php"; 

class CheckpointRegistryBuilderSaaS extends CheckpointRegistryBuilder
{
	public function build( & $registry )
	{
		$license = getFactory()->getObject('LicenseInstalled')->getAll()->get('LicenseType');
		
		if ( !in_array($license, array('LicenseSAASALM','LicenseSAASAgileTeam')) ) return;
		
		$registry->registerEntry( new LicenseSAASExpirationCheckpoint() );
	}
}