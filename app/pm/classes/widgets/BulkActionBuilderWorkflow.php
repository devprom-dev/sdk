<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderWorkflow extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();
 		if ( !$object instanceof MetaobjectStatable ) return;
 		if ( $object->getStateClassName() == '' ) return;
 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
 		
 		$state_it = $object->cacheStates();
		$transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('SourceState', $state_it->idsToArray()),
				new TransitionSourceStateSort()
			)
		);
		$attr_it = getFactory()->getObject('TransitionAttribute')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('Transition', $transition_it->idsToArray()),
                new TransitionAttributeSortClause()
			)
		);
        $attr_it->buildPositionHash(array('Transition'));

		while ( !$transition_it->end() )
		{
            $data = array_filter($attr_it->getSubset('Transition', $transition_it->getId()), function($value) {
                return $value['ReferenceName'] == 'Tasks';
            }) ;
			if ( count($data) > 0 ) {
				// skip those transitions where 'Tasks' attribute is defined as required one
				$transition_it->moveNext();
				continue;
			}
            $state_it->moveToId($transition_it->get('SourceState'));

			$registry->addWorkflowAction(
					$state_it->get('ReferenceName'),
					$transition_it->getDisplayName(),
					ObjectUID::getProject($state_it),
					$transition_it->getId()
				);
			$transition_it->moveNext();
		}
 	}
}