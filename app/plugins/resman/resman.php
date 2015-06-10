<?php

include "model/HumanResource.php";
include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class resmanPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'resman';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'resman.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('resman1');
 	}
 	
 	// plugin's order number in the list of plugins, is using to define dependencies between plugins
 	function getIndex()
 	{
 	    return parent::getIndex() + 5000;
 	}
 	
 	// returns plugins extentions for the corresponding application:
 	// PM - project management
 	// Admin - administration
 	// CO - common section
 	//
 	function getSectionPlugins()
 	{
 		return array( new resmanAdmin, new resmanPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
 	
    function checkLicense()
    {
		$license_it = getModelFactory()->getObject('LicenseState')->getAll();
 		return $license_it->get('IsValid') == 'Y' && in_array($license_it->get('LicenseType'), array('LicenseTrial','LicenseEnterprise','LicenseSAASALM', 'LicenseSAASALMMiddle', 'LicenseSAASALMLarge','LicenseDevOpsBoard'));
    }
}