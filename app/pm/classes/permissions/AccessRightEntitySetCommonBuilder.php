<?php
include_once "AccessRightEntitySetBuilder.php";

class AccessRightEntitySetCommonBuilder extends AccessRightEntitySetBuilder
{
    public function build( CommonAccessRight $set )
    {
     	$entities = array (
 			'pm_Project', 'pm_Version', 'pm_Release', 'pm_Build', 'pm_Task', 
			'pm_Participant', 'BlogPost',
			'pm_Milestone', 'pm_Function', 'pm_AccessRight', 
     		'pm_CustomReport', 'pm_Question', 'pm_Activity', 'pm_ProjectRole',
            'AutoAction', 'Snapshot', 'pm_Environment', 'Comment', 'pm_Invitation', 'Tag'
 		);

        if ( !getSession()->IsRDD() ) {
            $entities[] = 'pm_ChangeRequest';
        }

 		foreach( $entities as $entity ) {
 			$set->addObject(getFactory()->getObject($entity));
 		}
    }
}