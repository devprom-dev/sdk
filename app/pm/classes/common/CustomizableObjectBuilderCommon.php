<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilder.php";

class CustomizableObjectBuilderCommon extends CustomizableObjectBuilder
{
    public function build( CustomizableObjectRegistry & $set )
    {
        global $model_factory;
        
     	$entities = array (
			'pm_Question'
		);
 		
 		$methodology_it = $this->getSession()->getProjectIt()->getMethodologyIt();

		if ( $methodology_it->HasFeatures() ) $entities[] = 'pm_Function'; 

		if ( $methodology_it->HasReleases() ) $entities[] = 'Release';
		
		if ( $methodology_it->HasPlanning() ) $entities[] = 'Iteration';
		
		if ( $this->getSession()->getProjectIt()->get('IsKnowledgeUsed') == 'Y' ) $entities[] = 'ProjectPage';
		
		foreach( $entities as $entity_name )
		{
			$set->addObject( $model_factory->getObject($entity_name) );
		}
		
		if ( $methodology_it->HasTasks() )
		{
			$task = $model_factory->getObject('pm_Task');
			
			$set->addObject( 
				$task, '', $task->getDisplayName().': '.translate('любой тип')
			);
			
			$type_it = getFactory()->getObject('pm_TaskType')->getRegistry()->Query(
					array (
							new FilterBaseVpdPredicate()
					)
			);
						
			while ( !$type_it->end() )
			{
				$set->addObject( $task,
					strtolower(get_class($task)).':'.$type_it->get('ReferenceName'),
					$task->getDisplayName().': '.$type_it->getDisplayName()
				);
				
				$type_it->moveNext();
			}
		}
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$set->addObject( 
			$request, '', $request->getDisplayName().': '.translate('любой тип')
		);
		
		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
				array (
						new FilterBaseVpdPredicate()
				)
		);
		
		while ( !$type_it->end() )
		{
			$set->addObject( $request,
				strtolower(get_class($request)).':'.$type_it->get('ReferenceName'),
				$request->getDisplayName().': '.$type_it->getDisplayName()
			);
			
			$type_it->moveNext();
		}        
    }
}