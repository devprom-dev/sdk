<?php

include_once "LicenseRegistryBuilder.php";
include "LicenseTeam.php";
include "LicenseTeamSupported.php";
include "LicenseTeamSupportedCompany.php";
include "LicenseTeamSupportedUnlimited.php";

class LicenseRegistryBuilderCommon extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		$registry->addLicense( new LicenseTeam() );
	}
}
