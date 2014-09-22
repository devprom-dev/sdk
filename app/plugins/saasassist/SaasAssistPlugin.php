<?php

define( 'SAAS_DOMAIN', 'myalm.ru' );
define( 'SAAS_SENDER', 'noreply@projectscloud.ru' );

include "classes/licenses/LicenseRegistryBuilderSaaS.php";
include "classes/CheckpointRegistryBuilderSaaS.php";
include "SaasAssistCoPlugin.php";
include "SaasAssistAdminPlugin.php";
include "SaasAssistPmPlugin.php";

// define common plugin attributes
class SaasAssistPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'saasassist';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'saasassist.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('saasassist1');
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
 				new SaasAssistCoPlugin(), new SaasAssistAdminPlugin(), new SaasAssistPmPlugin()
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