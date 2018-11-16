<?php

include_once SERVER_ROOT_PATH."core/classes/history/ChangeLogEntitiesBuilder.php";

class ChangeLogEntitiesProjectBuilder extends ChangeLogEntitiesBuilder
{
    public function build( ChangeLogEntityRegistry $set )
    {
        $project_it = getSession()->getProjectIt();
        $methodology_it = $project_it->getMethodologyIt();
        
        $entities = array (
            'WikiPageFile',
			'WikiPageTrace',
 		    'WikiPageChange',
			'pm_Participant',
			'pm_Project',
			'pm_Methodology',
			'pm_ChangeRequest',
			'pm_ChangeRequestTrace',
			'pm_ChangeRequestLink',
            'pm_RequestTag',
            'pm_Milestone',
			'pm_Attachment',
			'pm_Question',
			'pm_ProjectRole',
			'pm_AccessRight',
			'pm_ObjectAccess',
			'pm_StateObject',
			'pm_State',
			'pm_Transition',
			'Comment',
			'pm_CustomAttribute',
        	'pm_Watcher',
        	'pm_FunctionTrace',
            'cms_Snapshot',
            'cms_SnapshotItem'
 		);
        
        if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' )
        {
            $entities[] = 'ProjectPage';
        }
        
        if ( $methodology_it->HasTasks() )
        {
            $entities[] = 'pm_Task';
            $entities[] = 'pm_TaskTrace';
        }
        
        if ( $methodology_it->HasPlanning() )
        {
            $entities[] = 'pm_Release';
        }
        
        if ( $methodology_it->HasReleases() )
        {
            $entities[] = 'pm_Version';
        }
        
        if ( $methodology_it->HasFeatures() )
        {
            $entities[] = 'pm_Function';
        }
        
        if ( $methodology_it->IsTimeTracking() )
        {
            $entities[] = 'pm_Activity';
        }
        
        foreach( $entities as $entity )
        {
            $set->add( $entity );
        }
    }
}