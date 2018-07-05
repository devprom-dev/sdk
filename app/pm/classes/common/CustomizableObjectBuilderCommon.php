<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilder.php";

class CustomizableObjectBuilderCommon extends CustomizableObjectBuilder
{
    public function build( CustomizableObjectRegistry & $set )
    {
     	$entities = array (
			'Question'
		);
 		
 		$methodology_it = $this->getSession()->getProjectIt()->getMethodologyIt();

		if ( $methodology_it->HasFeatures() ) $entities[] = 'Feature';
		if ( $methodology_it->HasReleases() ) $entities[] = 'Release';
		if ( $methodology_it->HasPlanning() ) $entities[] = 'Iteration';
        if ( $methodology_it->HasReleases() || $methodology_it->HasPlanning() ) $entities[] = 'Milestone';

		if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' ) $entities[] = 'ProjectPage';
		
		foreach( $entities as $entity_name ) {
			$set->add( $entity_name );
		}
		
		if ( $methodology_it->HasTasks() )
		{
			$set->add( 'Task', '', translate('Задача').': '.translate('любой тип') );
			
			$type_it = getFactory()->getObject('pm_TaskType')->getRegistry()->Query(
					array (
							new FilterBaseVpdPredicate()
					)
			);
			while ( !$type_it->end() )
			{
				$set->add( 'Task', 'task:'.$type_it->get('ReferenceName'), translate('Задача').': '.$type_it->getDisplayName());
				$type_it->moveNext();
			}
		}
		
		$set->add('Request', '', translate('Пожелание').': '.translate('любой тип'));
		
		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
				array (
						new FilterBaseVpdPredicate()
				)
		);
		while ( !$type_it->end() )
		{
			$set->add( 'Request', 'request:'.$type_it->get('ReferenceName'), translate('Пожелание').': '.$type_it->getDisplayName());
			$type_it->moveNext();
		}        
    }
}