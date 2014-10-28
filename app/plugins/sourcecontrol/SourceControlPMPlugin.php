<?php

include "classes/widgets/ModuleCategoryBuilderCode.php";
include "classes/widgets/FunctionalAreaDevelopmentBuilder.php";
include "classes/widgets/FunctionalAreaMenuDevelopmentBuilder.php";
include "classes/ReportsSourceControlBuilder.php";
include "classes/SharedObjectsSourceCodeBuilder.php";
include "classes/SearchableObjectsCodeBuilder.php";
include "classes/AccessRightEntitySetCodeBuilder.php";
include "classes/ProjectMetadataCodeBuilder.php";
include "classes/BuildCodeMetadataBuilder.php";
include "classes/SubversionModelBuilder.php";
include_once "classes/notificators/RevisionCommentActionsTrigger.php";
include "classes/HistoricalObjectsRegistryBuilderCode.php";
include "classes/ProjectLinkCodeMetadataBuilder.php";
include "classes/RequestCodeMetadataBuilder.php";
include "classes/TaskCodeMetadataBuilder.php";
include "classes/RequestTraceSourceCode.php";
include "classes/TaskTraceSourceCode.php";
include "classes/ProjectTemplateSectionsCodeRegistryBuilder.php";

class SourceControlPMPlugin extends PluginPMBase
{
    function getModules()
    {
        global $model_factory;
        	
        $modules = array (
            'files' =>
                array(
                        'includes' => array( 'sourcecontrol/views/SubversionFilesPage.php' ),
                        'classname' => 'SubversionFilesPage',
                        'title' => text('sourcecontrol3'),
                        'AccessEntityReferenceName' => 'pm_SubversionRevision',
                		'area' => ModuleCategoryBuilderCode::AREA_UID
                ),
            'revision' =>
                array(
                        'includes' => array( 'sourcecontrol/views/SubversionRevisionPage.php' ),
                        'classname' => 'SubversionRevisionPage',
                        'title' => text('sourcecontrol4'),
                        'AccessEntityReferenceName' => 'pm_SubversionRevision',
                		'area' => ModuleCategoryBuilderCode::AREA_UID
                )
        );

        $modules['connection'] = array(
                'includes' => array( 'sourcecontrol/views/SubversionConnectorPage.php' ),
                'classname' => 'SubversionConnectorPage',
                'title' => text('sourcecontrol28'),
        		'description' => text('sourcecontrol40'),
                'AccessEntityReferenceName' => 'pm_Subversion',
                'area' => ModuleCategoryBuilderCode::AREA_UID
        );

        return $modules;
    }

    function getBuilders()
    {
        return array (
        	// widgets
        	new ModuleCategoryBuilderCode(),
        		
            new SharedObjectsSourceCodeBuilder(),
            new FunctionalAreaDevelopmentBuilder(),
            new FunctionalAreaMenuDevelopmentBuilder(),
            new ReportsSourceControlBuilder(),
            new SearchableObjectsCodeBuilder(),
            new AccessRightEntitySetCodeBuilder(),
            new ProjectMetadataCodeBuilder(),
            new BuildCodeMetadataBuilder(),
            new RevisionCommentActionsTrigger( getSession() ),
        	new ProjectTemplateSectionsCodeRegistryBuilder(getSession()),
        		
        	// model extenders
        	new SubversionModelBuilder(),
        	new HistoricalObjectsRegistryBuilderCode(),
        	new ProjectLinkCodeMetadataBuilder(getSession()),
        	new RequestCodeMetadataBuilder(getSession()),
        	new TaskCodeMetadataBuilder(getSession())
        );
    }
}
