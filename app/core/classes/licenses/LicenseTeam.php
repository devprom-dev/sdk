<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include_once "LicenseTeamIterator.php";

class LicenseTeam extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(1270)', true, false, 'text(1273)', 0 );

 		$this->setAttributeVisible( 'LicenseKey', true ); 

 		$this->setAttributeVisible( 'LicenseValue', false ); 
	}
	
	function createIterator()
	{
		return new LicenseTeamIterator( $this );
	}
}
