<?php

include_once "LicenseDevOpsBoard.php";
include "LicenseDevOpsBoardUnlimitedIterator.php";
		
class LicenseDevOpsBoardUnlimited extends LicenseDevOpsBoard
{
	function __construct()
	{
		parent::__construct();
 		$this->setAttributeCaption( 'Caption', 'text(dobassist47)' );
 		$this->setAttributeDescription( 'Caption', 'text(dobassist48)' );
	}
	
	function createIterator()
	{
		return new LicenseDevOpsBoardUnlimitedIterator( $this );
	}
}

