<?php

include_once SERVER_ROOT_PATH.'cms/classes/ChangeLogNotificator.php';

class ScrumChangeNotificator extends ChangeLogNotificator
{
	function is_active( $object_it ) 
	{
		switch ( $object_it->object->getClassName() ) 
		{
			case 'pm_Scrum':
			    return true;
				
			default:
				return false;
		}
	}
}
