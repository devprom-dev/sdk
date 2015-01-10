<?php

include "COPlugin.php";

// define common plugin attributes
class accountPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'account';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'account.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('account1');
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
 		return array( new accountCo );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}