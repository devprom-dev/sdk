<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include "LicenseProcloudIterator.php";

class LicenseProcloud extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'Облако проектов', true, false, '', 0 );

 		$this->addAttribute( 'LicenseKey', 'VARCHAR', "Ключ", true, true );
	}

	function createIterator()
	{
		return new LicenseProcloudIterator( $this );
	}
}
