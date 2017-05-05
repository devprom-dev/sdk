<?php
include_once "LicenseRegistryBuilder.php";
include "LicenseTeam.php";

class LicenseRegistryBuilderCommon extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->addLicense( new LicenseTeam() );
	}
}
