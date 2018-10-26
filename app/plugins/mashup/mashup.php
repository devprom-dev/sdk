<?php
include "classes/MashupJavaScriptBuilder.php";
include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class mashupPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'mashup';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'mashup.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('mashup1');
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
 		return array( new mashupAdmin, new mashupPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}

 	function getBuilders()
    {
        return array(
            new MashupJavaScriptBuilder()
        );
    }
}