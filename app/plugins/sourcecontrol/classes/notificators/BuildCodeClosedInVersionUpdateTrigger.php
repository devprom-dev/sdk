<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class BuildCodeClosedInVersionUpdateTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1 )
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_Build' ) return;
	    
	    $data = $this->getRecordData();

	    if ( $data['BuildRevision'] == '' ) return;

	    $this->setClosedInVersion( $object_it, $data['BuildRevision'] );
	}
	
	function setClosedInVersion( $object_it, $commit_id )
	{
	    global $model_factory;
	    
	    $request = $model_factory->getObject('Request');
	    
	    $request->addFilter( new RequestCodeCommitPredicate($commit_id) ); 
	    
	    $request_it = $request->getAll();

	    while ( !$request_it->end() )
	    {
	        $request_it->modify( array( 
	                'ClosedInVersion' => $object_it->getDisplayName()
	        ));
	        
	        $request_it->moveNext();
	    }
	}
}
