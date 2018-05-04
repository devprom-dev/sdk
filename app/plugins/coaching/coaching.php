<?php
include "COPlugin.php";

define('COURSE_TEMPLATE_ID', 41);
define('COACH_USER_ID', 1);
define('COACHING_USER_GROUP', 2);

// define common plugin attributes
class coachingPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'coaching';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'coaching.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('coaching1');
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
 		return array( new coachingCo );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}