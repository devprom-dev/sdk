<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWebMethod.php";

class DuplicateIssuesWebMethod extends DuplicateWebMethod
{
	function getCaption() 
	{
		return text(867);
	}

	function getMethodName()
	{
		return parent::getMethodName().':LinkType';
	}
	
	function getReferences()
	{
		$references = array();
		
 	    $references[] = getFactory()->getObject('pm_IssueType');
 	    $references[] = getFactory()->getObject('Priority');
 	    
 	    $request = getFactory()->getObject('pm_ChangeRequest');
 	    $request->addFilter( new FilterInPredicate($this->getObjectIt()->idsToArray()) );
 	    
 	    $trace = getFactory()->getObject('pm_ChangeRequestTrace');
		$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $this->getObjectIt()->idsToArray()) ); 
		
		$attachment = getFactory()->getObject('pm_Attachment');
		$attachment->addFilter( new AttachmentObjectPredicate($this->getObjectIt()) );

 	    $task = getFactory()->getObject('Task');
 	    $task->addFilter( new FilterAttributePredicate('ChangeRequest', $this->getObjectIt()->idsToArray()) );
 	    
 	    $activity = getFactory()->getObject('Activity');
 	    $activity->addFilter( new ActivityRequestPredicate($this->getObjectIt()->idsToArray()) );

 	    $references[] = $request;
		$references[] = $trace;
		$references[] = $attachment;
 	    $references[] = $task;
 	    $references[] = $activity;		
 	    
 	    return $references;
	}
	
 	function duplicate( $project_it )
 	{
		$context = parent::duplicate( $project_it );
		
 	 	$map = $context->getIdsMap();

 	    $request = getFactory()->getObject('pm_ChangeRequest');
		$link = getFactory()->getObject('pm_ChangeRequestLink');
		$link_type = getFactory()->getObject('RequestLinkType');
		
		$type_it = $_REQUEST['LinkType'] != '' ? $link_type->getExact($_REQUEST['LinkType']) : $link_type->getEmptyIterator();
		if ( $type_it->getId() < 1 )
		{
			$type_it =  $link_type->getByRef('ReferenceName', 'duplicates');
		}
		
 	    foreach( $this->getObjectIt()->idsToArray() as $source_id )
 	    {
    		$link->add_parms( array( 
    		    'SourceRequest' => $source_id,
    			'TargetRequest' => $map[$request->getClassName()][$source_id],
    			'LinkType' => $type_it->getId() 
    		));
 	    }
 	    
 	    return $context;
 	}
}
