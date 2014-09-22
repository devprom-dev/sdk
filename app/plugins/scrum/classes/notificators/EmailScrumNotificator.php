<?php

include_once SERVER_ROOT_PATH."pm/classes/notificators/PMEmailNotificator.php";

include "EmailScrumHandler.php";

class EmailScrumNotificator extends PMEmailNotificator
{
 	function getHandler( $object_it ) 
 	{
 	    if ( $object_it->object->getEntityRefName() != 'pm_Scrum' ) return new EmailNotificatorHandler();
 	    
 		return new EmailScrumHandler();
 	}
 	
	function process( $action, $object_it, $prev_object_it ) 
	{
		if ( $object_it->object->getEntityRefName() != 'pm_Scrum' ) return;
		
		return parent::process( $action, $object_it, $prev_object_it );
	}
}
