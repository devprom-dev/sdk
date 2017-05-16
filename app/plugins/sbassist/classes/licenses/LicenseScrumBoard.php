<?php

include_once SERVER_ROOT_PATH.'core/classes/licenses/LicenseInstalled.php';
include "LicenseSAASBaseIterator.php";
		
class LicenseScrumBoard extends LicenseInstalled
{
	function __construct()
	{
		parent::__construct();

 		$this->addAttribute( 'Caption', 'VARCHAR', 'text(sbassist10)', true, false, 'text(sbassist11)', 0 );
 		$this->addAttribute( 'LicenseValue', 'INTEGER', "text(sbassist12)", true, true );
 		$this->addAttribute( 'LicenseKey', 'VARCHAR', "text(sbassist13)", true, true );
 		$this->addAttribute( 'LeftDays', 'VARCHAR', 'text(sbassist14)', true, false, 'text(sbassist15)' );
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
