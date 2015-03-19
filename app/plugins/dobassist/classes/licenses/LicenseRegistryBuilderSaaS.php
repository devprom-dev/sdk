<?php

include_once SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilder.php";
include_once "LicenseDevOpsBoard.php";

class LicenseRegistryBuilderSaaS extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->resetLicenses();
		$registry->addLicense( getFactory()->getObject('LicenseDevOpsBoard') );
	}
}
