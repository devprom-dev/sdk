<?php

include_once "LicenseIterator.php";
include_once "LicenseInstalledRegistry.php";
include_once "persisters/LicenseBaseUIDPersister.php";

class LicenseInstalled extends Metaobject
{
	function __construct()
	{
		parent::__construct('cms_License', new LicenseInstalledRegistry($this));
		
 		$this->addAttribute( 'InstallationUID', 'VARCHAR', translate('»дентификатор инсталл€ции'), true, false, '', 10 );
 					
 		$this->addPersister( new LicenseBaseUIDPersister() );
	}
	
	function createIterator()
	{
		return new LicenseIterator( $this );
	}
}
