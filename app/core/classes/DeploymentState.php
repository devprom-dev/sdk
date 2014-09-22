<?php

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class DeploymentState extends Metaobject
{
	function DeploymentState()
	{
		parent::Metaobject('entity');
	}
	
	static function IsInstalled()
	{
		return defined('DB_HOST') && DB_HOST != '?HOST';
	}
	
	static function IsScriptsCompleted()
	{
	    return file_exists(DOCUMENT_ROOT.'conf/logger.xml');
	}
	
	static function IsMaintained()
	{
	    $lock = new LockFileSystem(MAINTENANCE_LOCK_NAME);
	    
	    return $lock->Locked(300);
	}
	
	function IsActivated()
	{
		return getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') != '';
	}

	function IsLicensed()
	{
		return self::IsScriptsCompleted() && getFactory()->getObject('LicenseState')->getAll()->get('IsValid') == 'Y';
	}
	
	function IsReadyToBeUsed()
	{
		return $this->IsLicensed();
	}
}