<?php

include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class siamconsultPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'siamconsult';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'siamconsult.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('siamconsult1');
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
 		return array( new siamconsultAdmin, new siamconsultPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}