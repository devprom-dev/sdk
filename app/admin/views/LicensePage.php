<?php

include 'install/InstallLicenseTypeForm.php';
include 'install/InstallLicenseForm.php';
include 'LicenseForm.php';

class LicensePage extends AdminPage
{
    function LicensePage()
    {
        parent::AdminPage();
    }

    function getTable()
    {
        $license = getFactory()->getObject('LicenseInstalled');
        	
        if ( array_key_exists('LicenseType', $_REQUEST) && $_REQUEST['LicenseType'] == '' )
        {
            return new InstallLicenseTypeForm( $license );
        }
        else
        {
        	$class_name = getFactory()->getClass($_REQUEST['LicenseType']);

            if ( class_exists($class_name) )
            {
                $license_it = getFactory()->getObject($class_name);
            }
            else
            {
                $license_it = $license->getAll();
            }

            if ( array_key_exists('LicenseKey', $_REQUEST) )
            {
                return new InstallLicenseForm( $license_it );
            }
            	
            return new LicenseForm( $license_it );
        }
    }

    function getForm()
    {
        return null;
    }
}

