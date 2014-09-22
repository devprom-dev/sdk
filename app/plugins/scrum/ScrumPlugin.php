<?php

include "ScrumPMPlugin.php";

class ScrumPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'scrum';
 	}
 
  	function getFileName()
 	{
 		return 'scrum.php';
 	}
 	
 	function getCaption()
 	{
 		return 'Scrum';
 	}
 	
 	function getIndex()
 	{
 	    return parent::getIndex() + 1000;
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new ScrumPMPlugin );
 	}

    function getClasses()
    {
        return array (
            'pm_scrum' => 
                array ('Scrum', 'Scrum.php' ),
            'scrum' => 
                array ( 'Scrum', 'Scrum.php' ),
        );
    }
}