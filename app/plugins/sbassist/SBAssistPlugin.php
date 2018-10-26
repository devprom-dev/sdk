<?php

define('SAAS_SCHEME', 'http');
define('SAAS_PORT', 80);
define('SAAS_SENDER', 'noreply@devopsboard.com');

include "classes/ScriptCrispBuilder.php";
include_once "classes/licenses/LicenseRegistryBuilderSaaS.php";
include "classes/licenses/events/RenewSAASLicenseEventHandler.php";
include "classes/CheckpointRegistryBuilderSaaS.php";
include "SBAssistCoPlugin.php";
include "SBAssistAdminPlugin.php";
include "SBAssistPMPlugin.php";

// define common plugin attributes
class SBAssistPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'sbassist';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'sbassist.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('sbassist1');
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
            new SBAssistCoPlugin(), new SBAssistAdminPlugin(), new SBAssistPMPlugin()
 		);
 	}
 	
 	function IsUpdatedWithCore() {
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