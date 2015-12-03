<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class WikiBreakTraceTrigger extends SystemTriggersBase
{
    function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind == TRIGGER_ACTION_ADD and $object_it->object->getEntityRefName() == 'WikiPageChange' ) {
	    	// if page is changed notify user to review items covers the changed one
			$this->breakTraces( $object_it->getRef('WikiPage') );
	    }
	    
		if ( $kind == TRIGGER_ACTION_ADD and $object_it->object->getEntityRefName() == 'WikiPage' ) {
	    	// if new page is added notify user that branches should be reviewed
			$this->breakParentTraces( $object_it );
	    }

		if ( $kind != TRIGGER_ACTION_MODIFY and $object_it->object->getEntityRefName() == 'WikiPageFile' ) {
			// if page is changed notify user to review items covers the changed one
			$this->breakTraces( $object_it->getPageIt() );
		}
	}
    
	function breakTraces( $object_it )
	{
		if ( $object_it->getId() < 1 ) return;
		
	    $trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
	    		array (
	    			new FilterAttributePredicate('SourcePage', $object_it->getId() )
	    		)
	    	);
	    while ( !$trace_it->end() )
	    {
	    	if ( $trace_it->get('Baseline') > 0 || $trace_it->get('SourceBaseline') > 0 )
	    	{
	    		// skip traces linked to concrete versions
	    		$trace_it->moveNext();
	    		continue;
	    	}
	    	$trace_it->object->modify_parms( $trace_it->getId(), array(
	    			'IsActual' => 'N',
	    			'UnsyncReasonType' => 'text-changed'
	    	));
	    	$trace_it->moveNext();
	    }
	}

	function breakParentTraces( $object_it )
	{
		if ( $object_it->get('ParentPage') < 1 ) return;
		
	    $trace_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query( 
	    		array (
	    			new FilterAttributePredicate('SourcePage', $object_it->get('ParentPage') ),
	    			new FilterAttributePredicate('Type', 'branch')
	    		)
	    	);
	    
	    while ( !$trace_it->end() )
	    {
	    	$trace_it->object->modify_parms( $trace_it->getId(), array(
	    			'IsActual' => 'N',
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
 