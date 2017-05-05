<?php

include "SystemSettingsIterator.php";
include "persisters/SystemSettingsPersister.php";
include "persisters/SystemSettingsAdminProjectPersister.php";

class SystemSettings extends MetaobjectCacheable
{
 	function __construct() 
 	{
		parent::__construct('cms_SystemSettings');
		
		$this->addPersister( new SystemSettingsPersister() );
		$this->addPersister( new SystemSettingsAdminProjectPersister() );
	}

	function createIterator() 
	{
		return new SystemSettingsIterator( $this );
	}

	function IsDeletedCascade( $object )
	{
		return false;
	}
}