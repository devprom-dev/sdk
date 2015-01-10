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

	 	$projectrole = getFactory()->getObject('ProjectRole');
	 	$projectrole->addFilter( new ProjectRoleInheritedFilter() );
	 	
	 	// methodology settings
		$methodology = getFactory()->getObject('pm_Methodology');
		$methodology->addFilter( new FilterAttributePredicate('Project', $this->session->getProjectIt()->getId() ) );
	 	
	 	$items = array( 
	 		$project,
	 		$projectrole,
	 		getFactory()->getObject('pm_VersionSettings'),
 			getFactory()->getObject('pm_ProjectStage'),
	 		getFactory()->getObject('pm_IssueType'),
	 		getFactory()->getObject('TaskType'),
			getFactory()->getObject('TaskTypeStage'), 				
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
	 	
	 	$usersettings->addFilter(
				new FilterAttributePredicate('Participant', 
						array(
 	    						getSession()->getParticipantIt()->getId(), // search for user's settings 
 	    						'-1' // search for common settings
 	    				)
 	    		)
	 	);
	 	
	 	$usersettings->addSort(new SortAttributeClause('Participant.D')); // user settings overrides common settings 

	 	$items[] = $usersettings;
	 	
 		$registry->addSection($registry, 'Widgets', $items, true, text(1832));
    }
   
    private function buildTemplates( & $registry )
    {
 		$registry->addSection(
 				getFactory()->getObject('KnowledgeBaseTemplate'), 'Templates', array(), true, text(733)
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
	 		getFactory()->getObject('pm_TransitionRole'),
	 		getFactory()->getObject('pm_TransitionAttribute'),
	 		getFactory()->getObject('pm_TransitionPredicate'),
	 		getFactory()->getObject('pm_TransitionResetField'),
	 		getFactory()->getObject('pm_StateAction'),
	 		getFactory()->getObject('pm_StateAttribute')
 		);

 		$registry->addSection($registry, 'Workflow', $items, true, text(894));
    }

	private function buildArtefacts( & $registry )
    {
 		$items = array (
 			getFactory()->getObject('Release'),
 			getFactory()->getObject('Iteration'),
 			getFactory()->getObject('Tag'),
 			getFactory()->getObject('Feature'),
 			getFactory()->getObject('Request'),
 			getFactory()->getObject('RequestTag'),
 			getFactory()->getObject('WikiTag'),
			getFactory()->getObject('Task'),
 			getFactory()->getObject('TaskTraceTask'),
 			getFactory()->getObject('Milestone'),
 			getFactory()->getObject('RequestTraceMilestone'),
 			getFactory()->getObject('PMBlogPost'),
 			getFactory()->getObject('Attachment'),
 			getFactory()->getObject('Activity'),
 			getFactory()->getObject('PMEntityCluster'),
 			getFactory()->getObject('Comment')
 		);
 		
 		$log = getFactory()->getObject('ChangeLog');
 		$log->addFilter( new ChangeLogObjectFilter('request,task,pmblogpost,milestone') );
 		$items[] = $log; 
 		
		$registry->addSection($registry, 'ProjectArtefacts', $items, true, text(1834));
    }
}