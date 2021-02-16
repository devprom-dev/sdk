<?php

include "SetupLicenseForm.php";

class SetupLicenseController extends Page
{
 	function needDisplayForm() {
 		return true;
 	}

    function authorizationRequired()
    {
        if ( $_REQUEST['InstallationUID'] == INSTALLATION_UID ) return false;
        if ( getFactory()->getObject('User')->getRegistry()->Count() < 1 ) return false;
        if ( $_REQUEST['token1'] != '' && $_REQUEST['token2'] != '' )
        {
            $user_it = getFactory()->getObject('User')->getRegistry()->Query(
                array(
                    new UserDetailsPersister(),
                    new UserSessionPredicate(array($_REQUEST['token2']))
                )
            );
            if ( $user_it->getId() > 0 ) getSession()->open($user_it);
        }
        return parent::authorizationRequired();
    }

 	function render( $view = null )
 	{
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');

 		$license = getFactory()->getObject('LicenseInstalled');
 		$license->modify_parms($license->getAll()->getId(),
            array (
                'LicenseValue' => $_REQUEST['LicenseValue'],
                'LicenseKey' => $_REQUEST['LicenseKey'],
                'LicenseType' => $_REQUEST['LicenseType']
            )
 		);

        getCheckpointFactory()->getCheckpoint('CheckpointSystem')->executeDynamicOnly();

        getFactory()->getCacheService()->setReadonly();
        getFactory()->getCacheService()->invalidate();

        $this->persistAdminUser();

        parent::render();
 	}
 	
 	function getForm() {
 		return new SetupLicenseForm(getFactory()->getObject('License'));
 	}

    protected function persistAdminUser()
    {
        if ( $_REQUEST['UName'] == '' || $_REQUEST['UEmail'] == '' || $_REQUEST['ULogin'] == '' ) return;

        file_put_contents( DOCUMENT_ROOT . 'conf/admin.json',
            JsonWrapper::encode(
                array(
                    'Caption' => $_REQUEST['UName'],
                    'Login' => $_REQUEST['ULogin'],
                    'Email' => $_REQUEST['UEmail'],
                )
            )
        );
    }

    function __destruct()
    {
        if ( function_exists('opcache_reset') ) opcache_reset();
    }
}
