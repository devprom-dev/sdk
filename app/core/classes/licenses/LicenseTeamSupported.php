<?php

include_once "LicenseTeamSupportedIterator.php";

class LicenseTeamSupported extends LicenseTeam
{
	function __construct()
	{
		parent::__construct();
		$this->addAttribute( 'LicenseValue', 'VARCHAR', text(2066), true, true );
	}
	
	function createIterator()
	{
		return new LicenseTeamSupportedIterator( $this );
	}
}
