<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWebMethod.php";

class DuplicateIssuesWebMethod extends DuplicateWebMethod
{
	function getCaption() 
	{
		return text(867);
	}

	function getReferences()
	{
		global $model_factory;
		
		$references = array();
		
 	    $references[] = $model_factory->getObject('pm_IssueType');

 	    $references[] = $model_factory->getObject('Priority');
 	    
 	    $request = $model_factory->getObject('pm_ChangeRequest');
 	    
 	    $request->addFilter( new FilterInPredicate($this->getObjectIt()->idsToArray()) );
 	    
 	    $references[] = $request;
 	    
 	    $trace = $model_factory->getObject('pm_ChangeRequestTrace');
		
		$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $this->getObjectIt()->idsToArray()) ); 
 	    
		$references[] = $trace;
		
		$attachment = $model_factory->getObject('pm_Attachment');
				
		$attachment->addFilter( new AttachmentObjectPredicate($this->getObjectIt()) );
		
		$references[] = $attachment;

 	    $task = $model_factory->getObject('Task');
 	    
 	    $task->addFilter( new FilterAttributePredicate('ChangeRequest', $this->getObjectIt()->idsToArray()) );
 	    
 	    $references[] = $task;
 	    
 	    $activity = $model_factory->getObject('Activity');
 	    
 	    $activity->addFilter( new ActivityRequestPredicate($this->getObjectIt()->idsToArray()) );
 	    
 	    $references[] = $activity;		
		
 	    return $references;
	}
	
 	function duplicate( $project_it )
 	{
 	    global $model_factory;
 	    
		$context = parent::duplicate( $project_it );
		
 	 	$map = $context->getIdsMap();

 	    $request = $model_factory->getObject('pm_ChangeRequest');
 	    
		$link = $model_factory->getObject('pm_ChangeRequestLink');
		
 	    foreach( $this->getObjectIt()->idsToArray() as $source_id )
 	    {
    		$link->add_parms( array( 
    		    'SourceRequest' => $source_id,
    			'TargetRequest' => $map[$request->getClassName()][$source_id],
    			'LinkType' => 1 
    		));
 	    }
 	    
 	    return $context;
 	}
}
