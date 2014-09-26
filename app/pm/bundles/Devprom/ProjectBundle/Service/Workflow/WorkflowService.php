<?php

namespace Devprom\ProjectBundle\Service\Workflow;

class WorkflowService
{
	public function __construct( $object )
	{
		if ( !$object instanceof \MetaobjectStatable ) throw new \Exception('Statable object is required');
		 
		$this->object = $object;
		
		if ( $this->object->getStateClassName() == '' ) throw new \Exception('State class is not defined');
		
		$this->state_object = getFactory()->getObject($this->object->getStateClassName());
	}
	
	public function moveToState( $object_it, $target_state_ref_name, $comment = '' )
	{
		if ( $object_it->getId() == '' ) throw new \Exception('Nothing to move');
		
	    $source_it = $this->state_object->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('ReferenceName', $object_it->get('State')),
    					new \FilterVpdPredicate($object_it->get('VPD'))
    			)
    	);

    	\Logger::getLogger('System')->info( "[WorkflowService] Source state is ".$source_it->getId() );
	    	
	    $target_it = $this->state_object->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('ReferenceName', $target_state_ref_name),
    					new \FilterVpdPredicate($object_it->get('VPD'))
    			)
    	);
	    
	    if ( $target_it->getId() == '' ) throw new \Exception('Target state "'.$target_state_ref_name.'" is undefined');
	    
	    \Logger::getLogger('System')->info( "[WorkflowService] Target state is ".$target_it->getId() );
	    
	    $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
	    		array (
	    				new \FilterAttributePredicate('SourceState', $source_it->getId()),
	    				new \FilterAttributePredicate('TargetState', $target_it->getId())
	    		)
	    );

	    if ( $transition_it->getId() == '' )
	    {
	    	throw new \Exception('There is no transition from "'.$source_it->getDisplayName().'" to "'.$target_it->getDisplayName().'"');
	    }
	    
		$object_it->modify( array( 
		        'Transition' => $transition_it->getId(),
				'TransitionComment' => $comment 
		));
				
	    getFactory()->getEventsManager()->
	    		executeEventsAfterBusinessTransaction(
	    				$object_it->object->getExact($object_it->getId()), 'WorklfowMovementEventHandler'
		);
	}
	
	private $object = null;
	
	private $state_object = null;
}