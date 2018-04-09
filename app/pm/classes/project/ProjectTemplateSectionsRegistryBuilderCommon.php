<?php

include_once "ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateSectionsRegistryBuilderCommon extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
    	$this->buildSettings($registry);
    	
    	$this->buildWidgets($registry);
    	
    	$this->buildPermissions($registry);
    	
    	$this->buildWorkflow($registry);

 		$registry->addSection(getFactory()->getObject('pm_CustomAttribute'), 'Attributes', array(), true, text(1080));

    	$this->buildTemplates($registry);
 		
 		$this->buildArtefacts($registry);
	}
   
	private function buildSettings( & $registry )
    {
   	  	$project = getFactory()->getObject('pm_Project');
	 	$project->addFilter( new ProjectCurrentPredicate() );

	 	// methodology settings
		$methodology = getFactory()->getObject('pm_Methodology');
		$methodology->addFilter( new FilterAttributePredicate('Project', $this->session->getProjectIt()->getId() ) );
	 	
	 	$items = array( 
	 		$project,
            getFactory()->getObject('ProjectRoleInherited'),
	 		getFactory()->getObject('pm_IssueType'),
	 		getFactory()->getObject('TaskType'),
	 		$methodology
	 	);

		$projectpage = getFactory()->getObject('ProjectPage');
		$projectpage->addFilter( new WikiSectionFilter() );
		$projectpage->addSort( new SortDocumentClause() );
		$items[] = $projectpage;

 		$registry->addSection($registry, 'pm_Project', $items, true, text(734));
    }

    private function buildWidgets( & $registry )
    {
   		$items = array();
   		
   		$report = getFactory()->getObject('pm_CustomReport');
   		$report->addFilter( new CustomReportMyPredicate() );
   		
   		$items[] = $report;
   		
	 	// navigation settings
	 	$workspace = getFactory()->getObject('Workspace');

	 	$workspace_it = $workspace->getRegistry()->getDefault();
	 	
	 	$workspace->addFilter( new FilterInPredicate($workspace_it->idsToArray()) );
	 		
	 	$items[] = $workspace; 

	 	$workspace_menu = getFactory()->getObject('pm_WorkspaceMenu');
	 	
	 	$workspace_menu->addFilter( new FilterAttributePredicate('Workspace', $workspace_it->idsToArray()) );
	 	$workspace_menu->addSort( new SortOrderedClause() );
	 	
	 	$items[] = $workspace_menu;
	 	
	 	$workspace_item = getFactory()->getObject('pm_WorkspaceMenuItem');
	 	$workspace_item->addSort( new SortOrderedClause() );
	 	
	 	$workspace_item->addFilter( new FilterAttributePredicate('WorkspaceMenu', $workspace_menu->getAll()->idsToArray()) );
	 	
	 	$items[] = $workspace_item;

	 	// settings for reports and modules
	 	$usersettings = getFactory()->getObject('PMUserSettings');
	 	$usersettings->setRegistry( new PMUserSettingsExportRegistry() );

	 	$items[] = $usersettings;
	 	
 		$registry->addSection($registry, 'Widgets', $items, true, text(1832));
    }
   
    private function buildTemplates( & $registry )
    {
 		$registry->addSection(
            getFactory()->getObject('TextTemplate'), 'Templates', array(), true, text(733)
   		);
    }

    private function buildPermissions( & $registry )
    {
	 	$items = array (
	 			getFactory()->getObject('pm_AccessRight')
	 	);

 		$registry->addSection($registry, 'Permissions', $items, true, text(739));
   }

   private function buildWorkflow( & $registry )
   {
 		$items = array (
	 		getFactory()->getObject('pm_State'),
	 		getFactory()->getObject('pm_Transition'),
            getFactory()->getObject('ProjectRole'),
	 		getFactory()->getObject('pm_TransitionRole'),
	 		getFactory()->getObject('pm_TransitionAttribute'),
	 		getFactory()->getObject('pm_TransitionPredicate'),
	 		getFactory()->getObject('pm_TransitionResetField'),
	 		getFactory()->getObject('pm_StateAction'),
	 		getFactory()->getObject('pm_StateAttribute'),
            getFactory()->getObject('TaskTypeState'),
            getFactory()->getObject('IssueAutoAction')
        );
 		$registry->addSection($registry, 'Workflow', $items, true, text(894));
    }

	private function buildArtefacts( & $registry )
    {
 		$items = array (
 			getFactory()->getObject('Release'),
 			getFactory()->getObject('Iteration'),
 			getFactory()->getObject('Milestone'),
 			getFactory()->getObject('PMBlogPost'),
 			getFactory()->getObject('Tag')
 		);

		$registry->addSection($registry, 'ProjectArtefacts', $items, true, text(1834));
    }
}