<?php

include 'InstallForm.php';
include 'InstallFormComplete.php';
include 'InstallLicenseForm.php';
include 'InstallLicenseTypeForm.php';
include 'InstallPageSectionInfo.php';
include 'InstallPageSectionDocs.php';

class InstallPage extends AdminPage
{
	function InstallPage()
	{
		parent::AdminPage();
			
		$state = getFactory()->getObject('DeploymentState');
		if ( !$state->IsInstalled() ) {
			$this->addInfoSection(new DocumentationInfo);
			$this->addInfoSection(new InstallationInfo);
		}
	}

	function getTable()
	{
		$state = getFactory()->getObject('DeploymentState');
		
		if ( !$state->IsInstalled() )
		{
            return new InstallForm( getFactory()->getObject('cms_SystemSettings') );
		}
		
		if ( !$state->IsScriptsCompleted() )
		{
		    return new InstallFormComplete(getFactory()->getObject('cms_SystemSettings'));
		}

		if ( !$state->IsActivated() && $_REQUEST['LicenseType'] == '' )
		{
			return new InstallLicenseTypeForm( getFactory()->getObject('cms_SystemSettings') );
		}

		if ( !$state->IsLicensed() )
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
