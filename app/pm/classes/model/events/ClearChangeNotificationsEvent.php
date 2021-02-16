<?php
use Devprom\ProjectBundle\Service\Model\ModelChangeNotification;

class ClearChangeNotificationsEvent extends ObjectFactoryNotificator
{
 	function add( $object_it ) 
	{
	}

 	function modify( $prev_object_it, $object_it ) 
	{
	}

 	function delete( $object_it ) 
	{
	    $service = new ModelChangeNotification();
        $service->clearAll($object_it);
	}
}