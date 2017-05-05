<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include_once "LicenseTeamIterator.php";

class LicenseTeam extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(1270)', true, false, 'text(1273)', 0 );
        $this->addAttribute( 'LicenseValue', 'VARCHAR', text(2066), true, true );
        $this->addAttribute( 'LicenseKey', 'VARCHAR', translate("Ключ лицензии"), true, true );
	}
	
	function createIterator()
	{
		return new LicenseTeamIterator( $this );
	}
}
