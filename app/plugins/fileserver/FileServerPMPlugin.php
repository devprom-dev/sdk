<?php

include "classes/widgets/ModuleCategoryBuilderFileServer.php";
include "classes/widgets/FunctionalAreaFileServerBuilder.php";
include "classes/widgets/FunctionalAreaMenuFileServerBuilder.php";
include "classes/SearchableObjectsFilesBuilder.php";
include "classes/MethodologyFilesModelBuilder.php";
include "classes/ChangeLogEntitiesFileServerBuilder.php";
include "classes/CustomizableObjectBuilderFileServer.php";
include "classes/ProjectTemplateFileServerSectionsRegistryBuilder.php";
include "classes/ReportsFileServerBuilder.php";

class FileServerPMPlugin extends PluginPMBase
{
	private $enabled;

	function checkEnabled()
	{
		if ( isset($this->enabled) ) return $this->enabled;

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( is_object($methodology_it) ) {
			return ($this->enabled = $methodology_it->get('IsFileServer') == 'Y');
		}

		return false;
	}

 	function getModules()
 	{
		if ( !$this->checkEnabled() ) return array();

		$modules = array (
 			'files' => 
 				array(
 					'includes' => array( 'fileserver/views/ArtefactPage.php' ),
 					'classname' => 'ArtefactPage',
 					'title' => translate('Файлы'),
 					'description' => text('fileserver3'),
 				    'AccessEntityReferenceName' => 'pm_Artefact',
 					'area' => ModuleCategoryBuilderFileServer::AREA_UID
 					),
 			'folders' => 
 				array(
 					'includes' => array( 'fileserver/views/ArtefactTypePage.php' ),
 					'classname' => 'ArtefactTypePage',
 					'title' => translate('Каталоги'),
 					'description' => text('fileserver5'),
 				    'AccessEntityReferenceName' => 'pm_Artefact',
 					'area' => ModuleCategoryBuilderFileServer::AREA_UID
 					)
		);
 			
 		return $modules;
 	}
 	
 	function getBuilders()
 	{
 	    return array ( 
 	    		new ModuleCategoryBuilderFileServer(),
 	            new FunctionalAreaFileServerBuilder(),
 	            new FunctionalAreaMenuFileServerBuilder(),
 	            new SearchableObjectsFilesBuilder(getSession()),
 	            new MethodologyFilesModelBuilder(),
 	            new ChangeLogEntitiesFileServerBuilder(),
 	            new CustomizableObjectBuilderFileServer(getSession()),
 	    		new ProjectTemplateFileServerSectionsRegistryBuilder(getSession()),
 	    		new ReportsFileServerBuilder(getSession())
 	    );
 	}
}
