<?php

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
	    if ( $object_it->getId() == "" ) return;
	    DAL::Instance()->Query(
	        " DELETE FROM ObjectChangeNotification WHERE ObjectId = ".$object_it->getId()." AND ObjectClass = '".get_class($object_it->object)."' "
        );
	}
}