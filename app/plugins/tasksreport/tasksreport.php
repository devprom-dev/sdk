<?php
define('CONTRACT_REFNAME', 'article');
define('REGION_REFNAME', 'dzo');

include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class tasksreportPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'tasksreport';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'tasksreport.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('tasksreport1');
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
 		return array( new tasksreportAdmin, new tasksreportPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}