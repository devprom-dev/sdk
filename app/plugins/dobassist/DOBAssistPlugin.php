<?php

define( 'SAAS_DOMAIN', 'devopsboard.com' );
define( 'SAAS_SCHEME', 'https' );
define( 'SAAS_SENDER', 'noreply@devopsboard.com' );
define( 'SAAS_ROOT', '/home/devopsboard/' );

include_once "classes/licenses/LicenseRegistryBuilderSaaS.php";
include "classes/licenses/events/RenewSAASLicenseEventHandler.php";
include "classes/CheckpointRegistryBuilderSaaS.php";
include "DOBAssistCoPlugin.php";
include "DOBAssistAdminPlugin.php";
include "DOBAssistPmPlugin.php";

// define common plugin attributes
class DOBAssistPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'dobassist';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'dobassist.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('dobassist1');
 	}
 	
 	// plugin's order number in the list of plugins, is using to define dependencies between plugins
 	function getIndex()
 	{
 	    return parent::getIndex() + 55000;
 	}
 	
 	// returns plugins extentions for the corresponding application:
 	// PM - project management
 	// Admin - administration
 	// CO - common section
 	//
 	function getSectionPlugins()
 	{
 		return array(
 				new DOBAssistCoPlugin(), new DOBAssistAdminPlugin(), new DOBAssistPmPlugin()
 		);
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
 	
    function getBuilders()
    {
        return array( 
        		new LicenseRegistryBuilderSaaS(),
        		new CheckpointRegistryBuilderSaaS()
        );
    }
}