<?php

class KanbanRequestBoard extends RequestBoard
{
	function buildBoardAttributeIterator()
	{
		return getFactory()->getObject($this->getBoardAttributeClassName())->getRegistry()->Query(
				array (
						new FilterVpdPredicate(array_shift($this->getTable()->getProjectVpds())),
						new SortAttributeClause('OrderNum')
				)
		);
	}

 	function getBoardNames()
 	{
 		$state_it = getFactory()->getObject('IssueState')->getRegistry()->Query(
			array (
				new FilterVpdPredicate($this->getTable()->getProjectVpds())
			)
		);
		$lengths = array();
		while( !$state_it->end() ) {
			$lengths[$state_it->get('ReferenceName')] += $state_it->get('QueueLength');
			$state_it->moveNext();
		}
 		
 		$names = parent::getBoardNames();
 		
 		$ref_names = $this->getBoardStates();
 		foreach ( $ref_names as $ref_name )
 		{
 			$objects = $this->getObjectsInState($ref_name);
 			
 			$title = $names[$ref_name]; 
 			if ( $lengths[$ref_name] > 0 )
 			{
	 			$title .= ' '.
	 				str_replace('%2', $lengths[$ref_name],
	 					str_replace('%1', $objects,
	 						str_replace(' ', '&nbsp;', text('kanban8'))));
	 						
	 			if ( $lengths[$ref_name] < $objects ) {
	 				$title = '<div style="color:red;font-weight:bold;">'.$title.'</div>';
	 			}
 			}
 			else {
	 			$title .= ' '.
 					str_replace('%1', $objects,
 						str_replace(' ', '&nbsp;', text('kanban16')));
 			}
 			$names[$ref_name] = $title;
 		}

 		return $names;
 	}
 	
 	function getObjectsInState( $referenceName )
 	{
 		$object = new MetaobjectStatable($this->getObject()->getEntityRefName());
		$object->disableVpd();
		$object->resetPersisters();
		
		$object->addFilter( new StatePredicate($referenceName) );
		$object->addFilter( new FilterVpdPredicate($this->getTable()->getProjectVpds()) );

		$count_aggregate = new AggregateBase( 'State' );
		$object->addAggregate( $count_aggregate );

		$cnt = $object->getAggregated()->get( $count_aggregate->getAggregateAlias() );
		return $cnt == '' ? 0 : $cnt;
 	}
}