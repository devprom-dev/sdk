<?php
include_once "LicenseRegistryBuilder.php";
include "LicenseTeam.php";
include "LicenseEnterprise.php";
include "LicenseTrial.php";

class LicenseRegistryBuilderCommon extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->addLicense( new LicenseTeam() );
        $registry->addLicense( new LicenseEnterprise() );
        $registry->addLicense( new LicenseTrial() );
	}
}
