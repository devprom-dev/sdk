<?php

include "PMPlugin.php";

// define common plugin attributes
class ProductTourPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'producttour';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'producttour.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('producttour1');
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
 		return array( new ProductTourPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}