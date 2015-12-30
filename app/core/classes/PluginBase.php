<?php

include ('PluginSectionBase.php');
include ('PluginAPISectionBase.php');
include ('PluginCoSectionBase.php');
include ('PluginPMSectionBase.php');
include ('PluginAdminSectionBase.php');

class PluginBase
{
    function __construct()
    {
    }
    
 	function getNamespace()
 	{
 	}
 	
 	function getCaption()
 	{
 	}
 	
 	function getDescription()
 	{
 	}
 	
 	function getIndex()
 	{
 		return 100;
 	}
 	
 	function getFileName()
 	{
 	}
 	
 	function getSectionPlugins()
 	{
 	}
 	
 	function getAuthorizationFactories()
 	{
		return array();	
 	}
 	
 	function getClass( $class )
 	{
 	}

 	function getClasses()
 	{
 		return array();
 	}
 	
 	function getBuilders()
 	{
 	    return array();
 	}
 	
	function getObjectUrl( $object_it )
	{
	}

 	function IsLicensed()
 	{
		return true; 		
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return true;
 	}

	function checkLicense() {
		return true;
	}

	function checkEnabled() {
		return true;
	}

	function getHeaderMenus() {
		return array();
	}
}