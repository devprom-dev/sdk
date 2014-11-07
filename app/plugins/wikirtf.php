<?php

///////////////////////////////////////////////////////////////////////////////
class WikiRTFPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'wrtf';
 	}
 
  	function getFileName()
 	{
 		return basename(__FILE__);
 	}
 	
 	function getCaption()
 	{
 		return 'wrtf';
 	}
 	
 	function getSectionPlugins()
 	{
 		return array();
 	}

 	function IsUpdatedWithCore()
 	{
 		return true;
 	}
}
