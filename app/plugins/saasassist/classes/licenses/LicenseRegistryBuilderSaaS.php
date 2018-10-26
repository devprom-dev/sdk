<?php

include_once SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilder.php";
include_once "LicenseSAASALM.php";
include_once "LicenseSAASALMMiddle.php";
include_once "LicenseSAASALMLarge.php";
include_once "LicenseSAASALM20.php";

class LicenseRegistryBuilderSaaS extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->resetLicenses();
		$registry->addLicense( getFactory()->getObject('LicenseSAASALM') );
	}
}
