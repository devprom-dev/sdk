<?php

include_once SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilder.php";

class LicenseRegistryBuilderProcloud extends LicenseRegistryBuilder
{
	public function build( LicenseRegistry & $registry )
	{
		global $model_factory;
		
		$registry->addLicense( $model_factory->getObject('LicenseProcloud') );
	}
}
