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
    	
    	$this->buildKnowledgeBase($registry);
    	
    	$this->buildProjectRoles($registry);
    	
    	$this->buildWorkflow($registry);
    	
 		$registry->addSection(new Metaobject('cms_Resource'), 'Terminology', array(), true, text(940));

 		$registry->addSection(getFactory()->getObject('pm_CustomAttribute'), '', array(), true, text(1080));
 		
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
	 		getFactory()->getObject('pm_VersionSettings'),
	 		getFactory()->getObject('pm_ProjectStage'),
	 		getFactory()->getObject('pm_IssueType'),
	 		getFactory()->getObject('TaskType'),
	 		$methodology
	 	);
	 	
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
   
    private function buildKnowledgeBase( & $registry )
    {
 		$registry->addSection(
 				getFactory()->getObject('KnowledgeBaseTemplate'), 'KnowledgeBaseTemplate', array(), true, text(733)
   		);

	 	$projectpage = getFactory()->getObject('ProjectPage');

	 	$projectpage->addFilter( new WikiSectionFilter() );
		$projectpage->addSort( new SortDocumentClause() );
	 	
 		$registry->addSection($projectpage, 'ProjectPage', array(), true, text(736));
    }
   
    private function buildProjectRoles( & $registry )
    {
	 	$projectrole = getFactory()->getObject('ProjectRole');
	 	$projectrole->addFilter( new ProjectRoleInheritedFilter() );
	 	
	 	$items = array (
	 			$projectrole,
	 			getFactory()->getObject('pm_AccessRight')
	 	);

 		$registry->addSection($registry, 'ProjectRole', $items, true, text(739));
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
			getFactory()->getObject('TaskTypeStage'), 				
 			getFactory()->getObject('Tag'),
 			getFactory()->getObject('Feature'),
 			getFactory()->getObject('Request'),
 			getFactory()->getObject('RequestTag'),
 			getFactory()->getObject('WikiTag'),
			getFactory()->getObject('Task'),
 			getFactory()->getObject('TaskTraceTask'),
 			getFactory()->getObject('Milestone'),
 			getFactory()->getObject('Attachment'),
 			getFactory()->getObject('Activity'),
 			getFactory()->getObject('Comment'),
 			getFactory()->getObject('PMEntityCluster'),
 			getFactory()->getObject('Snapshot'),
 			getFactory()->getObject('SnapshotItem'),
 			getFactory()->getObject('SnapshotItemValue')
 		);
 		
 		$registry->addSection($registry, 'ProjectArtefacts', $items, true, text(1834));
    }
}