<?php

include "CustomsPMPlugin.php";

class CustomsPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'customs';
 	}
 
  	function getFileName()
 	{
 		return 'customs.php';
 	}
 	
 	function getCaption()
 	{
 		return 'Customs';
 	}
 	
 	function getIndex()
 	{
 	    return parent::getIndex() + 1000;
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new CustomsPMPlugin );
 	}
}