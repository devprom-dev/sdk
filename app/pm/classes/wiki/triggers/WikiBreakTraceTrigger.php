<?php

class WikiBreakTraceTrigger extends SystemTriggersBase
{
    function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $kind == TRIGGER_ACTION_ADD and $object_it->object->getEntityRefName() == 'WikiPage' ) {
	    	// if new page is added notify user that branches should be reviewed
			$this->breakParentTraces( $object_it );
	    }
	}
    
	function breakParentTraces( $object_it )
	{
		if ( $object_it->get('ParentPage') < 1 ) return;
		$data = $this->getRecordData();
		
	    $trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
	    		array (
	    			new FilterAttributePredicate('SourcePage', $object_it->get('ParentPage') ),
	    			new FilterAttributePredicate('Type', 'branch')
	    		)
	    	);
	    
	    while ( !$trace_it->end() )
	    {
	        if ( $data['ReintegratedTraceId'] ==  $trace_it->getId() ) {
                $trace_it->moveNext();
                continue;
            }

	    	$trace_it->object->modify_parms( $trace_it->getId(), array(
                'IsActual' => 'N',
                'RecordModified' => $trace_it->get('RecordModified'),
                'UnsyncReasonType' => 'structure-append'
	    	));
	    	
	    	$trace_it->moveNext();
	    }
	    
	    $trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
	    		array (
	    			new FilterAttributePredicate('TargetPage', $object_it->get('ParentPage') ),
	    			new FilterAttributePredicate('UnsyncReasonType', 'structure-append')
	    		)
	    	);
	    
	    while ( !$trace_it->end() )
	    {
	    	$trace_it->object->modify_parms( $trace_it->getId(), array(
	    			'IsActual' => 'Y',
	    			'UnsyncReasonType' => ''
	    	));
	    	
	    	$trace_it->moveNext();
	    }
	}
}
 