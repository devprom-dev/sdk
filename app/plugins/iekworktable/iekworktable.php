<?php

define(DEPT_ATTRIBUTE_NAME, 'Departament');
define(CUSTOMER_ATTRIBUTE_NAME, 'Customer');
define(SERVICE_DESK_PROJECT, 'SDK');

include "iekworktablecoplugin.php";

// define common plugin attributes
class iekworktable extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'iekworktable';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'iekworktable.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('iekworktable1');
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
 		return array( new iekworktablecoplugin );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}