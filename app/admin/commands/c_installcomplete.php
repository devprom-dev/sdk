<?php

include(SERVER_ROOT_PATH.'admin/install/InstallationFactory.php');

class InstallComplete extends CommandForm
{
	function validate()
	{
		return getFactory()->getObject('DeploymentState')->IsInstalled();
	}

	function create()
	{
		// run post installation scripts
		$result = array();
			
		// setup server constants
		$this->setupCustomerCredentials();
		$this->setupDefaultLanguage( $_REQUEST['language'] );

		getFactory()->getPluginsManager()->buildPluginsList();
		
		$installation_factory = InstallationFactory::getFactory();
		if ( !$installation_factory->install( $result ) ) {
		    $this->replyError(str_replace('%1', join(', ', $result), text(1053)));
		}
		
	    $clear_cache_action = new ClearCache();
	    $clear_cache_action->install();
		
		// report result of the operation
		$this->replyRedirect( '?', text(443) );
	}
	
	protected function setupCustomerCredentials()
	{
		$settings_file_path = DOCUMENT_ROOT.'settings_const.php';

		$file_content = file_get_contents($settings_file_path);

		if( $file_content == "" ) $this->replyError( text(1031).': '.$settings_file_path );

		$file_content = str_replace("?UUID2", $this->gen_uuid(), $file_content);
		$file_content = str_replace("?UUID8", $this->gen_uuid(), $file_content);
		$file_content = str_replace("?UUID10", $this->gen_uuid(), $file_content);
		$file_content = str_replace("?UUID11", $this->gen_uuid(), $file_content);

		file_put_contents($settings_file_path, $file_content);
	}
	
	protected function setupDefaultLanguage( $lang )
	{
		DAL::Instance()->Query("UPDATE cms_SystemSettings SET Language = ".($lang == 2 ? 2 : 1));
	}

	function gen_uuid()
	{
		list($usec, $sec) = explode(" ",microtime());
		return md5(strftime('%d.%m.%Y.%M.%H.%S').((float)$usec + (float)$sec).rand());
	}
}