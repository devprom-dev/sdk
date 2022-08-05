<?php
include "model/ExampleEntity.php";
include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class example5Plugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'example5';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'example5.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('example51');
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
 		return array( new example5Admin, new example5PM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}