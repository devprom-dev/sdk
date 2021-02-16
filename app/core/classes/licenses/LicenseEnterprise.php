<?php
include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include "LicenseEnterpriseIterator.php";
		
class LicenseEnterprise extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(1271)', true, false, 'text(1274)', 0 );
 		$this->addAttribute( 'LicenseValue', 'VARCHAR', "text(ee94)", true, true );
 		$this->addAttribute( 'LicenseKey', 'LARGETEXT', "text(ee95)", true, true );
	}

	function createIterator()
	{
		return new LicenseEnterpriseIterator( $this );
	}
}
