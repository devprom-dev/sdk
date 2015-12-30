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

		$state_it = getFactory()->getObject($object->getStateClassName())->getRegistry()->Query(
			array (
				new FilterVpdPredicate(),
				new SortAttributeClause('VPD')
			)
		);
		$transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('SourceState', $state_it->idsToArray()),
				new TransitionSourceStateSort()
			)
		);
		while ( !$transition_it->end() )
		{
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