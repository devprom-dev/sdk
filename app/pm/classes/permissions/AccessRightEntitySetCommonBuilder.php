<?php
include_once "AccessRightEntitySetBuilder.php";

class AccessRightEntitySetCommonBuilder extends AccessRightEntitySetBuilder
{
    public function build( CommonAccessRight $set )
    {
     	$entities = array (
 			'pm_Project', 'pm_Version', 'pm_Release', 'pm_Build', 'pm_Task', 
			'pm_ChangeRequest', 'pm_Participant', 'BlogPost', 'pm_Artefact',
			'pm_Milestone', 'pm_Function', 'pm_AccessRight', 
     		'pm_CustomReport', 'pm_Question', 'pm_Activity', 'pm_ProjectRole',
            'AutoAction', 'Snapshot', 'pm_Environment', 'Comment'
 		);
 		
 		foreach( $entities as $entity ) {
 			$set->addObject(getFactory()->getObject($entity));
 		}
    }
}