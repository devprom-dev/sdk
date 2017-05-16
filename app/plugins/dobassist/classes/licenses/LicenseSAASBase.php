<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include "LicenseSAASBaseIterator.php";
		
class LicenseSAASBase extends LicenseInstalled
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
		return new LicenseSAASBaseIterator( $this );
	}
	
 	function checkLicense( $iterator )
 	{
 		return $this->getLeftDays( $iterator ) > 0;
 	}
 	
 	function getLeftDays( $iterator = null )
 	{
 		if ( !is_object($iterator) ) {
 			$iterator = getFactory()->getObject('LicenseInstalled')->getAll(); 
 		}

 		if ( $iterator->getId() == '') return 0;

 		$license_data = @file_get_contents(SERVER_ROOT_PATH.'conf/license.dat');
 		if ( $license_data == '' ) return $iterator->getLeftDays();
 		
 		$license = unserialize($license_data);
 		return $license['leftdays'];
 	}
}
