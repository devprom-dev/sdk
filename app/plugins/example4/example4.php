<?php

define(DEPT_ATTRIBUTE_NAME, 'Departament');
define(CUSTOMER_ATTRIBUTE_NAME, 'Customer');
define(SERVICE_DESK_PROJECT, 'SDK');

include "coplugin.php";

// define common plugin attributes
class example4 extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'example4';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'example4.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('example41');
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
 		return array( new example4coplugin );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}