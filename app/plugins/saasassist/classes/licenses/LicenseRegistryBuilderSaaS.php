<?php

include_once SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilder.php";
include_once "LicenseSAASAgileTeam.php";
include_once "LicenseSAASALM.php";

class LicenseRegistryBuilderSaaS extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->resetLicenses();
		
		$registry->addLicense( getFactory()->getObject('LicenseSAASAgileTeam') );
		$registry->addLicense( getFactory()->getObject('LicenseSAASALM') );
	}
}
