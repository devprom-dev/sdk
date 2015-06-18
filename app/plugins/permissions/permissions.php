<?php

include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";
include "classes/PortfolioMyProjectsBuilder.php";

// define common plugin attributes
class permissionsPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'permissions';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'permissions.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('permissions1');
 	}
 	
 	// plugin's order number in the list of plugins, is using to define dependencies between plugins
 	function getIndex()
 	{
 	    return parent::getIndex() + 5;
 	}
 	
 	// returns plugins extentions for the corresponding application:
 	// PM - project management
 	// Admin - administration
 	// CO - common section
 	//
 	function getSectionPlugins()
 	{
 		return array( new permissionsCo(), new permissionsPM(), new permissionsAdmin() );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}