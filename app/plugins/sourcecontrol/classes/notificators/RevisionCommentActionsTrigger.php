<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

define('TASKS_UID', 1);
define('TASKS_STATE', 3);
define('TASKS_TIME', 5);
define('TASKS_COMMENT', 8);
define('TASKS_FREE_TEXT', 10);

class RevisionCommentActionsTrigger extends SystemTriggersBase
{
    private $session;
    
    function __construct( PMSession $session )
    {
        $this->session = $session;
        
        parent::__construct();
    }
    
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_ADD ) return;

	    if ( !is_a($object_it->object, 'SubversionRevision') ) return;
	    
		$descriptions = explode(PHP_EOL, $object_it->get('Description'));
	    foreach( $descriptions as $description ) {
			$this->parseDescription( $description, $object_it );
	    }
	}
	
	function getExpression()
	{
		$expression = '/([TI]{1}-[\d]+)((\#(resolve|%STATES)[^\#]*)|(\#time\s+([\d]+d\s?)?([\d]+h)?[^\#]*)|(\#comment\s+([^$]+))|([^\#]+))*/i';
		
		$states = array_merge(
				getFactory()->getObject('Request')->getStates(),
				getFactory()->getObject('Task')->getStates()
		);
		
		$expression = str_replace('%STATES', join('|',array_unique($states)), $expression);
		
		$this->info( "Regex used: ".$expression );
		
		return $expression;
	}
	
	function parseDescription( $description, &$object_it )
	{
		if ( !preg_match_all($this->getExpression(), $description, $match_result) ) {
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
	    $comment_added = false;
	    $target_state = trim($match_result[TASKS_STATE][$index], ' #');
	    if ( $target_state != '' )
	 	{
	 		$this->info( "Need to change state: ".$target_state );
	 	    $this->moveObjects( $objects, $comments, $committer_it, $target_state );
	 	    $comment_added = true;
	 	}	    

		$methodology_it = $this->session->getProjectIt()->getMethodologyIt();
	    if ( $methodology_it->IsTimeTracking() && is_object($committer_it) && $committer_it->getId() > 0 && $spent_hours > 0 )
	    {
	        $this->addWorkLog( $objects, $committer_it->getRef('SystemUser'), $spent_hours, !$comment_added ? $comments : '' );
	        $comment_added = true;
	    }
	    
	    $free_text = trim(array_shift($match_result[TASKS_FREE_TEXT]));
	    if ( !$comment_added && $comments != '' ) {
	    	$this->addComment($objects, $committer_it, $comments);
	    }
	    else if ( $free_text != '' ) {
	    	$this->addComment($objects, $committer_it, $free_text);
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
	
	function addWorkLog( & $objects, & $committer_it, $spent_hours, $comments )
	{
	    global $model_factory;
	    
 		foreach ( $objects as $object_it )
 		{
	 		$activity_parms = array( 
		 		'ReportDate' => 'NOW()',
		 		'Capacity' => max((float) $spent_hours, 0.0),
		 		'Completed' => 'Y',
	 			'Description' => $comments 
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

	    	try
	    	{
				$service = new WorkflowService($object_it->object, Logger::getLogger('SCM'));
				
		    	$service->moveToState($object_it, $target_state, $comments);
	    	}
	    	catch( \Exception $e )
	    	{
	    		Logger::getLogger('SCM')->error($e->getMessage());
	    	}
	    }
	}
	
	function addComment( $objects, $committer_it, $text )
	{
 		foreach ( $objects as $object_it )
 		{
			getFactory()->getObject('Comment')->add_parms(
				array (
					'ObjectId' => $object_it->getId(),
					'ObjectClass' => get_class($object_it->object),
					'AuthorId' => $committer_it->get('SystemUser'),
					'Caption' => $text
				)
			);
 		}
	}
	
 	function info( $message )
 	{
		$log = Logger::getLogger('SCM');
		
		$log->info( $message );
 	}
}
