<?php

include_once "AccessRightEntitySetBuilder.php";

class AccessRightEntitySetCommonBuilder extends AccessRightEntitySetBuilder
{
    public function build( CommonAccessRight $set )
    {
        global $model_factory;
        
     	$entities = array (
 			'pm_Project', 'pm_Version', 'pm_Release', 'pm_Build', 'pm_Task', 
			'pm_ChangeRequest', 'pm_Participant', 'BlogPost', 'pm_Artefact',
			'pm_Milestone', 'pm_Function', 'pm_AccessRight', 
     		'pm_CustomReport', 'pm_Question', 'pm_Activity'
 		);
 		
 		foreach( $entities as $entity )
 		{
 			$set->addObject($model_factory->getObject($entity));
 		}
        
    }
}