<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class UpdateBlockedParticipantsEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !in_array($object_it->object->getEntityRefName(), array('cms_BlackList')) ) return;

	    $part = getFactory()->getObject('pm_Participant');
	    
	    $participant_it = $part->getRegistry()->Query(
	    		array (
	    				new FilterAttributePredicate('SystemUser', $object_it->get('SystemUser'))
	    		)
	    );
	    
	    while( !$participant_it->end() )
	    {
	    	$part->modify_parms($participant_it->getId(), 
	    			array (
	    					'IsActive' => ($kind == TRIGGER_ACTION_DELETE ? 'Y' : 'N')
	    			)
	    	);
	    	
	    	$participant_it->moveNext();
	    }
	}
}