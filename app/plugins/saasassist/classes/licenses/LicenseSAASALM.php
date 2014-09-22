<?php

include_once "LicenseSAASBase.php";
include "LicenseSAASALMIterator.php";
		
class LicenseSAASALM extends LicenseSAASBase
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(saasassist10)', true, false, 'text(saasassist11)', 0 );
 		
 		$this->addAttribute( 'LicenseValue', 'INTEGER', "text(saasassist12)", true, true );

 		$this->addAttribute( 'LicenseKey', 'VARCHAR', "text(saasassist13)", true, true );
	
 		$this->addAttribute( 'LeftDays', 'VARCHAR', 'text(saasassist14)', true, false, 'text(saasassist15)' );
	}

	function createIterator()
	{
		return new LicenseSAASALMIterator( $this );
	}
}

