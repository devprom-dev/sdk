<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include "LicenseTrialIterator.php";

class LicenseTrial extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(1269)', true, false, 'text(1272)', 0 );
 		$this->addAttribute( 'LicenseValue', 'VARCHAR', "text(ee94)", true, true );
 		$this->addAttribute( 'LicenseKey', 'LARGETEXT', "text(ee95)", true, true );
 		$this->addAttribute( 'LeftDays', 'VARCHAR', 'text(ee103)', true, false, 'text(ee104)' );
	}

	function createIterator()
	{
		return new LicenseTrialIterator( $this );
	}
}
