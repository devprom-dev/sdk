<?php

class KanbanRequestBoard extends RequestBoard
{
 	function getBoardNames()
 	{
 		global $model_factory;
 		
 		$state = $model_factory->getObject('IssueState');
 		
 		$state_it = $state->getAll();
 		
 		$names = parent::getBoardNames();
 		
 		$ref_names = $this->getBoardStates();

 		foreach ( $ref_names as $ref_name )
 		{
 			$state_it->moveTo('ReferenceName', $ref_name);

 			if ( $state_it->end() ) continue;
 			
 			$length = $state_it->get('QueueLength');

 			$objects = $this->getObjectsInState($state_it);
 			
 			$title = $names[$ref_name]; 
 			
 			if ( $length > 0 )
 			{
	 			$title .= ' '.
	 				str_replace('%2', $length, 
	 					str_replace('%1', $objects,
	 						str_replace(' ', '&nbsp;', text('kanban8'))));
	 						
	 			if ( $length < $objects )
	 			{
	 				$title = '<div style="color:red;font-weight:bold;">'.$title.'</div>';
	 			}
 			}
 			else
 			{
	 			$title .= ' '.
 					str_replace('%1', $objects,
 						str_replace(' ', '&nbsp;', text('kanban16')));
 			}

 			$names[$ref_name] = $title;
 		}

 		return $names;
 	}
 	
 	function getObjectsInState( $state_it )
 	{
 		$object = new MetaobjectStatable($this->getObject()->getEntityRefName());
		
		$object->resetPersisters();
		
		$object->addFilter( new StatePredicate($state_it->get('ReferenceName')) );
		
		$count_aggregate = new AggregateBase( 'State' );
		
		$object->addAggregate( $count_aggregate );

		$it = $object->getAggregated();
		
		$cnt = $it->get( $count_aggregate->getAggregateAlias() );
 	    
		return $cnt == '' ? 0 : $cnt;
 	}
}