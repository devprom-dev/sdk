<?php

namespace Devprom\ProjectBundle\Service\Workflow;

class WorkflowService
{
	const RESOLVE = 'resolve';
	
	public function __construct( $object, $logger = null )
	{
		$this->logger = is_object($logger) ? $logger : \Logger::getLogger('System');  
		
		if ( !$object instanceof \MetaobjectStatable ) throw new \Exception('Statable object is required');
		 
		$this->object = $object;
		
		if ( $this->object->getStateClassName() == '' ) throw new \Exception('State class is not defined');
		
		$this->state_object = getFactory()->getObject($this->object->getStateClassName());
	}

	public function getStateObject()
	{
		return $this->state_object;
	}
	
	public function moveToState( $object_it, $target_state_ref_name, $comment = '', $parms = array(), $fire_event = true )
	{
		$target_state_ref_name = !is_array($target_state_ref_name) ? preg_split('/,/',$target_state_ref_name) : $target_state_ref_name;
		
		if ( $object_it->getId() == '' ) throw new \Exception('Nothing to move');

	    $source_it = $this->state_object->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('ReferenceName', $object_it->get('State')),
    					new \FilterVpdPredicate($object_it->get('VPD')),
    					new \SortOrderedClause()
    			)
    	);

    	$this->logger->info( "[WorkflowService] Source state is ".$source_it->getId() );
    	
	    $target_it = $this->state_object->getRegistry()->Query(
    			array (
    					in_array(self::RESOLVE, $target_state_ref_name)
    							? new \FilterAttributePredicate('IsTerminal', 'Y')
    							: new \FilterAttributePredicate('ReferenceName', $target_state_ref_name),
    					new \FilterVpdPredicate($object_it->get('VPD')),
    					new \SortOrderedClause()
    			)
    	);
	    
	    if ( $target_it->getId() == '' ) throw new \Exception('Target state "'.join(',',$target_it->fieldToArray('Caption')).'" is undefined');
	    
	    $this->logger->info( "[WorkflowService] Target states are ".join(',',$target_it->idsToArray()) );
	    
	    $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
	    		array (
	    				new \FilterAttributePredicate('SourceState', $source_it->getId()),
	    				new \FilterAttributePredicate('TargetState', $target_it->idsToArray())
	    		)
	    );

	    if ( $transition_it->getId() == '' )
	    {
			$this->logger->error('There is no transition from "'.$source_it->getDisplayName().'" to "'.$target_it->getDisplayName().'"');
			return false;
	    }
	    
		$result = $object_it->object->modify_parms($object_it->getId(),
				array_merge( $parms, 
						array( 
					        'Transition' => $transition_it->getId(),
							'TransitionComment' => $comment 
						)
				)
		);

		if ( $result < 1 ) {
			$this->logger->error('Unable move object from "'.$source_it->getDisplayName().'" to "'.$target_it->getDisplayName().'"');
			return false;
		}

		$object_it = $object_it->object->getExact($object_it->getId());
		if ( $fire_event )
		{
			getFactory()->getEventsManager()->
	    		executeEventsAfterBusinessTransaction($object_it, 'WorklfowMovementEventHandler');
		}
        return true;
	}
	
	private $object = null;
	private $state_object = null;
	private $logger = null;
}