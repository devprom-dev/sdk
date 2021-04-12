<?php

include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class example1Plugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'example1';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'example1.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('example11');
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
 		return array( new example1Admin, new example1PM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}