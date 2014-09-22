<?php

include_once SERVER_ROOT_PATH."cms/classes/EmailNotificator.php";

include "AdminEmailNotificatorHandler.php";
include "AdminUserHandler.php";

class AdminEmailNotificator extends EmailNotificator
{
 	var $handlers, $common_handler;
 	
	function __construct() 
	{
		parent::__construct();

		$this->common_handler = new AdminEmailNotificatorHandler;
		
		$this->handlers = array( 'cms_User' => new AdminUserHandler	);
	}
 	
 	function getHandler( $object_it ) 
 	{
		$handler = $this->handlers[$object_it->object->getClassName()];
		return is_object($handler) ? $handler : $this->common_handler;
 	}
 	
	function process( $action, $object_it, $prev_object_it ) 
	{
		if ( !array_key_exists( $object_it->object->getClassName(), $this->handlers ) ) return;
		parent::process( $action, $object_it, $prev_object_it );
	}

	function getSender( $object_it, $action ) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getSender( $object_it, $action );
	}

	function getSubject( $object_it, $prev_object_it, $action, $recipient )
	{
		$subject = parent::getSubject($object_it, 
			$prev_object_it, $action, $recipient);
			
		$handler = $this->getHandler( $object_it );
		
		return $handler->getSubject( $subject, 
			$object_it, $prev_object_it, $action, $recipient );
	}

	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getRecipientArray( $object_it, $prev_object_it, $action );
	}
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getBody( $action, $object_it, $prev_object_it, $recipient );
	}	

	function getMailBox($object_it) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getMailBox();
	}
}
