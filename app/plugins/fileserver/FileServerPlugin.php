<?php

include "classes/Artefact.php";
include "classes/ArtefactType.php";
include "classes/DataModelRegistryBuilderFileServer.php";

include "FileServerPMPlugin.php";

class FileServerPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'fileserver';
 	}
 
  	function getFileName()
 	{
 		return 'fileserver.php';
 	}
 	
 	function getCaption()
 	{
 		return text('fileserver1');
 	}
 	
    function getIndex()
    {
        return parent::getIndex() + 500;
    }
 	
 	function getSectionPlugins()
 	{
 		return array( new FileServerPMPlugin() );
 	}

 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
 	
 	function checkEnabled()
 	{
        $session = getSession();
         
        if ( !is_a($session, 'PMSession') ) return false;
        
        $project_it = $session->getProjectIt();

        if ( !is_object($project_it) ) return false;
        
        return $project_it->getMethodologyIt()->get('IsFileServer') == 'Y';
 	}
 	
 	function getClasses()
 	{
 		return array (
 			'pm_artefact' => 
 				array( 'Artefact', 'Artefact.php', '' ),

 			'pm_artefacttype' => 
 				array( 'ArtefactType', 'ArtefactType.php', '' )
 		);
 	}
 	
 	function getBuilders()
 	{
 		return array ( 
 				new DataModelRegistryBuilderFileServer() 
 		);
 	}
}
