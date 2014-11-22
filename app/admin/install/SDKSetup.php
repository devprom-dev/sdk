<?php

class SDKSetup extends Installable 
{
	function check()
	{
		return true;
	}
	
	function skip()
    {
        return INSTALLATION_UID != '{75E2A7D9-4BD8-4bdc-8392-D1AA2308A383}' || DB_NAME == 'projectscloud';
    }

    function install()
    {
 		$user_id = $this->createUser( 'Administrator', 'admin', 'Administrator' );

 		$this->updateSystemSettings();
 		
		getCheckpointFactory()->getCheckpoint('CheckpointSystem')->executeDynamicOnly();
		
		getSession()->close();
		
		getSession()->open(getFactory()->getObject('User')->getExact($user_id));

		$installation_factory = InstallationFactory::getFactory();
		    
		$clear_cache_action = new ClearCache();
		    
		$clear_cache_action->install();
		
		return true;
	}
 	
 	protected function createUser( $name, $login, $email )
 	{
 		return getFactory()->getObject('User')->add_parms(
 				array (
 						'Caption' => $name,
 						'Login' => $login,
 						'Email' => $email,
 						'Password' => $login,
 						'IsAdmin' => 'Y'
 				)
 		);
 	}
 	
 	protected function updateSystemSettings()
 	{
 		$settings = getFactory()->getObject('cms_SystemSettings');
 		
 		$settings->modify_parms($settings->getAll()->getId(),
 				array (
 						'Caption' => 'Devprom SDK',
 						'EmailSender' => 'admin',
 						'AdminEmail' => 'Administrator',
 						'ServerName' => EnvironmentSettings::getServerName(),
 						'ServerPort' => 80
 				)
 		);
 	}
}
