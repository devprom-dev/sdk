<?php

include 'InstallForm.php';
include 'InstallFormComplete.php';
include 'InstallLicenseForm.php';
include 'InstallLicenseTypeForm.php';

class InstallPage extends AdminPage
{
	function getTable()
	{
		if ( !\DeploymentState::IsInstalled() )
		{
            return new InstallForm( getFactory()->getObject('cms_SystemSettings') );
		}
		
		if ( !\DeploymentState::IsScriptsCompleted() )
		{
		    return new InstallFormComplete(getFactory()->getObject('cms_SystemSettings'));
		}

		if ( !\DeploymentState::Instance()->IsActivated() && $_REQUEST['LicenseType'] == '' )
		{
			return new InstallLicenseTypeForm( getFactory()->getObject('cms_SystemSettings') );
		}

		if ( !\DeploymentState::Instance()->IsLicensed() )
		{
			if ( $_REQUEST['LicenseType'] == '' )
			{
				return new InstallLicenseTypeForm( getFactory()->getObject('cms_SystemSettings') );
			}

			$class_name = getFactory()->getClass($_REQUEST['LicenseType']);
			
			if ( class_exists($class_name, false) )
			{
				$license_it = getFactory()->getObject($class_name);
			}
			else
			{
				$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
			}
			
			return new InstallLicenseForm( $license_it );
		}
	}
	
	function getForm()
	{
		return null;
	}
	
	function getTitle()
	{
		return translate('Установка');
	}
}
