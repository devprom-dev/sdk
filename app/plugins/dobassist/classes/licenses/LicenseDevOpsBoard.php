<?php

include_once "LicenseSAASBase.php";
include "LicenseDevOpsBoardIterator.php";
		
class LicenseDevOpsBoard extends LicenseSAASBase
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(dobassist10)', true, false, 'text(dobassist11)', 0 );
 		$this->addAttribute( 'LicenseValue', 'INTEGER', "text(dobassist12)", true, true );
 		$this->addAttribute( 'LicenseKey', 'VARCHAR', "text(dobassist13)", true, true );
 		$this->addAttribute( 'LeftDays', 'VARCHAR', 'text(dobassist14)', true, false, 'text(dobassist15)' );
	}

	function createIterator()
	{
		return new LicenseDevOpsBoardIterator( $this );
	}
}

