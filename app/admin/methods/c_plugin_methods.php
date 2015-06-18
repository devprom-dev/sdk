<?php

include SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';

class TogglePluginWebMethod extends WebMethod
{
	var $filename, $plugin;

	function TogglePluginWebMethod ( $filename = '' )
	{
		$this->filename = $filename;
			
		parent::WebMethod();
	}

	function getCaption()
	{
		global $plugins;
			
		if ( $plugins->pluginEnabled($this->filename) )
		{
			return translate('Отключить');
		}
		else
		{
			return translate('Подключить');
		}
	}

	function execute_request()
	{
		$this->execute($_REQUEST['file']);
	}

	function execute( $file_name )
	{
		global $plugins;
			
	    // clear old cache
	    $installation_factory = InstallationFactory::getFactory();
	    
	    $clear_cache_action = new ClearCache();
	    
		$plugins->enablePlugin($file_name, !$plugins->pluginEnabled($file_name));

	    $clear_cache_action->install();
	}
}
