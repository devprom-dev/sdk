<?php

class StateTransitionsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         return array ();
     }
     
     function add( $id, $parms )
     {
     	$this->persistTransitions( $id, $parms );
     }
     
     function modify( $id, $parms )
     {
     	$this->persistTransitions( $id, $parms );
     }
     
     protected function persistTransitions( $id, $parms )
     {
     	$transition = getFactory()->getObject('Transition');
     	$transition_it = $transition->getRegistry()->Query(
     			array ( new FilterAttributePredicate('SourceState', $id) )
     		);
     	
		$state_it = $this->getObject()->getRegistry()->Query(
				array (
						new FilterBaseVpdPredicate()
				)
			);
		while( !$state_it->end() )
		{
			if ( strtolower($parms['ForwardRequired'.$state_it->get('ReferenceName')]) == 'on' )
			{ 
				$transition_it->moveTo('TargetState', $state_it->getId());
				if ( $transition_it->getId() == '' ) {
					$transition->add_parms(
							array (
									'Caption' => $state_it->getHtmlDecoded('Caption'),
									'SourceState' => $id,
									'TargetState' => $state_it->getId()
							)
					);
				}
			}
			else
			{
				$transition_it->moveTo('TargetState', $state_it->getId());
				if ( $transition_it->getId() != '' ) {
					$transition->delete($transition_it->getId());
				}
			}
			$state_it->moveNext();
		}

     	$transition_it = $transition->getRegistry()->Query(
     			array ( new FilterAttributePredicate('TargetState', $id) )
     		);
     	$state_it->moveFirst();
     	while( !$state_it->end() )
		{
			if ( strtolower($parms['BackwardRequired'.$state_it->get('ReferenceName')]) == 'on' )
			{ 
				$transition_it->moveTo('SourceState', $state_it->getId());
				if ( $transition_it->getId() == '' ) {
					$transition->add_parms(
							array (
									'Caption' => $parms['Caption'],
									'SourceState' => $state_it->getId(),
									'TargetState' => $id
							)
					);
				}
			}
			else
			{
				$transition_it->moveTo('SourceState', $state_it->getId());
				if ( $transition_it->getId() != '' ) {
					$transition->delete($transition_it->getId());
				}
			}
			$state_it->moveNext();
		}
     }
}