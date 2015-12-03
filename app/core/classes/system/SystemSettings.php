<?php

include "SystemSettingsIterator.php";
include "persisters/SystemSettingsPersister.php";
include "persisters/SystemSettingsAdminProjectPersister.php";

class SystemSettings extends MetaobjectCacheable
{
 	function __construct() 
 	{
		parent::__construct('cms_SystemSettings');
		
		$this->addAttribute( 'EmailSender', 'VARCHAR', text(1223), true, false, text(1224), 35 );
		$this->addAttribute( 'ServerName', 'VARCHAR', text(1076), true, false, text(1218) );
		$this->addAttribute( 'ServerPort', 'VARCHAR', text(1151), true, false, text(465) );
		$this->addAttribute( 'TimeZoneUTC', 'VARCHAR', text(2026), true, false, text(2027) );
		$this->addAttribute( 'PasswordLength', 'INTEGER', text(2070), true, false, text(2071) );

		$this->setAttributeRequired( 'OrderNum', false );
		$this->setAttributeCaption( 'AllowToChangeLogin', text(1345) );
		$this->setAttributeDescription( 'AdminEmail', text(1375) );
		
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