<?php

include "classes/widgets/ModuleCategoryBuilderFileServer.php";
include "classes/widgets/FunctionalAreaFileServerBuilder.php";
include "classes/widgets/FunctionalAreaMenuFileServerBuilder.php";
include "classes/SearchableObjectsFilesBuilder.php";
include "classes/ProjectMetadataFilesBuilder.php";
include "classes/ChangeLogEntitiesFileServerBuilder.php";
include "classes/CustomizableObjectBuilderFileServer.php";
include "classes/ProjectTemplateFileServerSectionsRegistryBuilder.php";
include "classes/ReportsFileServerBuilder.php";

class FileServerPMPlugin extends PluginPMBase
{
 	function getModules()
 	{
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
 	            new ProjectMetadataFilesBuilder(),
 	            new ChangeLogEntitiesFileServerBuilder(),
 	            new CustomizableObjectBuilderFileServer(getSession()),
 	    		new ProjectTemplateFileServerSectionsRegistryBuilder(getSession()),
 	    		new ReportsFileServerBuilder(getSession())
 	    );
 	}
}
