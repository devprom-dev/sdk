<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderWorkflow extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();
 		if ( !$object instanceof MetaobjectStatable ) return;
 		if ( $object->getStateClassName() == '' ) return;
 		
 		$state_it = $object->cacheStates();
		$trans_attr = getFactory()->getObject('TransitionAttribute');
			
		while( !$state_it->end() )
		{
			$transition_it = $state_it->getTransitionIt();
			while ( !$transition_it->end() )
			{
				$required_attrs = $trans_attr->getRegistry()->Query(
							array (
									new FilterAttributePredicate('Transition', $transition_it->getId())
							)
					)->fieldToArray('ReferenceName');
				
				if ( in_array('Tasks', $required_attrs, true) )
				{
					// skip those transitions where 'Tasks' attribute is defined as required one
					$transition_it->moveNext();
					continue;
				}

				$registry->addWorkflowAction(
						$state_it->get('ReferenceName'), 
						$transition_it->getDisplayName(),
						ObjectUID::getProject($state_it), 
						$transition_it->getId()
					);
				$transition_it->moveNext();
			}
			$state_it->moveNext();
		}
 	}
}