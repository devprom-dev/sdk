<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

define('TASKS_UID', 1);
define('TASKS_STATE', 3);
define('TASKS_TIME', 5);
define('TASKS_COMMENT', 8);

class RevisionCommentActionsTrigger extends SystemTriggersBase
{
    private $session;
    
    function __construct( PMSession & $session )
    {
        $this->session =& $session;
        
        parent::__construct();
    }
    
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_ADD ) return;

	    if ( !is_a($object_it->object, 'SubversionRevision') ) return;
	    
	    $this->parseDescription( $object_it );
	}
	
	function getExpression()
	{
		$expression = '/([TI]{1}-[\d]+[^\#]*)((\#(resolve|%STATES)[^\#]*)|(\#time\s+([\d]+d\s?)?([\d]+h)?[^\#]*)|(\#comment\s+([^$]+)))*/i';
		
		$states = array_merge(
				getFactory()->getObject('Request')->getStates(),
				getFactory()->getObject('Task')->getStates()
		);
		
		$expression = str_replace('%STATES', join('|',array_unique($states)), $expression);
		
		$this->info( "Regex used: ".$expression );
		
		return $expression;
	}
	
	function parseDescription( & $object_it )
	{
		if ( !preg_match_all($this->getExpression(), $object_it->get('Description'), $match_result) )
		{
			$this->info( "Skip user comments: ".$object_it->get('Description') );
			
			return;
		}
		
		$this->info( "Make actions: ".var_export($match_result, true) );

		// bind the commit to the given objects
	 	$objects = $this->bindObjects( $object_it, $match_result[TASKS_UID] );

	 	if ( count($objects) < 1 )
	 	{
	 		$this->info( "Object not found: ".$match_result[TASKS_UID] );
	 		
	 		return;
	 	}

		$this->info( "Objects found: ".count($objects) );
	 	
		// check for user actions
		$index = count($match_result[TASKS_UID]) - 1;
		
		// check for time spending
	 	if ( strpos($match_result[TASKS_TIME][$index], '#time') !== false )
	 	{
	 		$spent_hours = ((int) str_replace('d', '', $match_result[TASKS_TIME+1][$index])) * 8
	 			+ (int) str_replace('h', '', $match_result[TASKS_TIME+2][$index]);
	 	}

		$this->info( "Work log: ".$spent_hours );
	 	
		// check for comments
	 	if ( strpos($match_result[TASKS_COMMENT][$index], '#comment') !== false )
	 	{
	 		$comments = $match_result[TASKS_COMMENT+1][$index];
	 	}

		$this->info( "Comment is given: ".$comments );
	 	
		// get committer 
	    $committer_it = $object_it->getRef('Participant');
	    
	    if ( $committer_it->getId() < 1 )
	    {
	        $this->info( "Activity comitter wasnt found" );
	    }

	    // make actions
	    $methodology_it = $this->session->getProjectIt()->getMethodologyIt();
	 	
	    if ( $methodology_it->IsTimeTracking() && is_object($committer_it) && $committer_it->getId() > 0 && $spent_hours > 0 )
	    {
	        $this->addWorkLog( $objects, $committer_it, $spent_hours );
	    }
	    
	    $target_state = trim($match_result[TASKS_STATE][$index], ' #');
	    
	    if ( $target_state != '' )
	 	{
	 		$this->info( "Need to change state: ".$target_state );
	 		
	 	    $this->moveObjects( $objects, $comments, $committer_it, $target_state );
	 	}	    
	}

	function bindObjects( & $revision_it, $uids )
	{
	    global $model_factory;
	    
	    $result = array();
	    
	    $uid = new ObjectUID;
	    
		foreach ( $uids as $object_uid )
		{
			$object_it = $uid->getObjectIt( trim($object_uid) );
			
			if ( $object_it->getId() < 1 ) continue;
			
			switch ( $object_it->object->getEntityRefName() )
			{
				case 'pm_Task':
	 				$trace = $model_factory->getObject('TaskTraceSourceCode');
	 				
	 				$trace_it = $trace->getByRefArray( 
	 					array( 'Task' => $object_it->getId(),
	 						   'ObjectId' => $revision_it->getId(),
	 						   'ObjectClass' => 'SubversionRevision' ) 
	 					);
	
					if ( $trace_it->count() < 1 )
					{
		 				$trace->add_parms( 
		 					array( 'Task' => $object_it->getId(),
		 						   'ObjectId' => $revision_it->getId(),
		 						   'ObjectClass' => 'SubversionRevision' ) 
		 					);
		 				
		 				$result[] = $object_it->copy();
					}
					
					break;
					
				case 'pm_ChangeRequest':
	 				$trace = $model_factory->getObject('RequestTraceSourceCode');
	 				
	 				$trace_it = $trace->getByRefArray( 
	 					array( 'ChangeRequest' => $object_it->getId(),
	 						   'ObjectId' => $revision_it->getId(),
	 						   'ObjectClass' => 'SubversionRevision' ) 
	 					);
	
					if ( $trace_it->count() < 1 )
					{
		 				$trace->add_parms( 
		 					array( 'ChangeRequest' => $object_it->getId(),
		 						   'ObjectId' => $revision_it->getId(),
		 						   'ObjectClass' => 'SubversionRevision' ) 
		 					);
		 				
		 				$result[] = $object_it->copy();
					}
					
					break;
					
				default:
					continue;
			}
		}	

		return $result;
	}
	
	function addWorkLog( & $objects, & $committer_it, $spent_hours )
	{
	    global $model_factory;
	    
 		foreach ( $objects as $object_it )
 		{
	 		$activity_parms = array( 
		 		'ReportDate' => 'NOW()',
		 		'Capacity' => max((float) $spent_hours, 0.0),
		 		'Completed' => 'Y' 
	 		);
	 		 
	 		switch ( $object_it->object->getEntityRefName() )
	 		{
	 			case 'pm_ChangeRequest':

	 			    $activity = $model_factory->getObject('ActivityRequest');
	 				
	 				break;

	 			case 'pm_Task':
	 				
	 			    $activity = $model_factory->getObject('ActivityTask');
	 				 
	 				break;
	 		}
	 		
	 		$activity_parms['Task'] = $object_it->getId();
	 		$activity_parms['Participant'] = $committer_it->getId();

	        $this->info( "Activity commiter is: ".$committer_it->getDisplayName() );
 			
			$result = $activity->add_parms( $activity_parms ); 
			
	        $this->info( "Activity object has been created: ".$result );
	 	}
	}
	
	function moveObjects( & $objects, $comments, & $committer_it, $target_state )
	{
	    if ( is_object($committer_it) && $committer_it->getId() > 0 )
	    {
	        $this->info( "Set session participant: ".$committer_it->getId() );
	        
	        $this->session->setParticipantIt( $committer_it );
	    }
	    
	    foreach( $objects as $object_it )
	    {
	    	$this->info( "Process object: ".$object_it->getId() );
	    	
	    	$state_object = getFactory()->getObject($object_it->object->getStateClassName());
	    	
	    	$source_it = $state_object->getRegistry()->Query(
	    			array (
	    					new FilterAttributePredicate('ReferenceName', $object_it->get('State')),
	    					new FilterVpdPredicate($object_it->get('VPD'))
	    			)
	    	);

	    	$this->info( "Source state: ".$source_it->getId() );
	    	
		    $target_it = $state_object->getRegistry()->Query(
	    			array (
	    					$target_state == 'resolve'
	    							? new FilterAttributePredicate('IsTerminal', 'Y')
	    							: new FilterAttributePredicate('ReferenceName', $target_state),
	    					new FilterVpdPredicate($object_it->get('VPD')),
	    					new SortAttributeClause('OrderNum')
	    			)
	    	);
	    	
	    	$this->info( "Target state: ".$target_it->getId() );
		    
		    $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
		    		array (
		    				new FilterAttributePredicate('SourceState', $source_it->getId()),
		    				new FilterAttributePredicate('TargetState', $target_it->getId())
		    		)
		    );

	    	if ( $transition_it->getId() > 0 )
	    	{
				$object_it->modify( array( 
				        'Transition' => $transition_it->getId(),
						'TransitionComment' => $comments 
				));
				
			    getFactory()->getEventsManager()->
			    		executeEventsAfterBusinessTransaction(
			    				$object_it->object->getExact($object_it->getId()), 'WorklfowMovementEventHandler'
	   					);
			    		
			    $this->info( "Object has been moved" );
	    	}
	    	else
	    	{
	    		$this->info( "Transition not found" );
	    	}
	    }
	}
	
 	function info( $message )
 	{
		$log = Logger::getLogger('SCM');
		
		$log->info( $message );
 	}
}
