<?php

include_once "LicenseDevOpsBoard.php";
include "LicenseDevOpsBoardUnlimitedIterator.php";
		
class LicenseDevOpsBoardUnlimited extends LicenseDevOpsBoard
{
	function __construct()
	{
		parent::__construct();
 		$this->setAttributeCaption( 'Caption', 'text(dobassist47)' );
	}
	
	function createIterator()
	{
		return new LicenseDevOpsBoardUnlimitedIterator( $this );
	}
}

