<?php

class ModelEventsManager
{
 	private $notificators = array();
 	
 	private $cascade = false;
	
 	public function setCascade( $cascade = true )
 	{
 		$this->cascade = $cascade;
 	}

 	public function getCascade()
 	{
 		return $this->cascade;
 	}
 	
	function registerNotificator( &$notificator_object ) 
	{
		$this->notificators[get_class($notificator_object)] = $notificator_object;
	}
	
	function removeNotificator( $notificator_object )
	{
		if ( is_object($notificator_object) )
		{
			unset($this->notificators[get_class($notificator_object)]);
		}
		else
		{
			unset($this->notificators[$notificator_object]);
		}
	}
	
	function getNotificators( $base_class_name = '' )
	{
	    if ( $base_class_name == '' ) return is_array($this->notificators) ? $this->notificators : array();

		$notificators = array();
		
		foreach ( $this->notificators as $key => $notificator )
		{
			if ( is_a($notificator, $base_class_name) )
			{
				array_push ( $notificators, $this->notificators[$key] );
			}
		}
		
		return $notificators;
	}
	
	function notificationEnabled( $object_it, & $notificator )
	{
		$disabled = $object_it->object->getDisabledNotificators();
		
		foreach ( $disabled as $base_class )
		{
			if ( is_a($notificator, $base_class) )
			{
				return false;
			}
		}
		
		return true;
	}
	
 	function notify_object_add( $object_it, $data = array() ) 
 	{
		foreach( $this->getNotificators() as $notificator )
		{
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;
			
			$notificator->setRecordData( $data );

			$notificator->add( $object_it );
		}
	}

 	function notify_object_modify( $prev_object_it, $object_it, $data = array() ) 
 	{
		foreach( $this->getNotificators() as $notificator )
 	    {
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;
			
			$notificator->setRecordData( $data );
 	    		
			$notificator->modify( $prev_object_it, $object_it );
		}
	}

 	function notify_object_delete( $object_it ) 
 	{
		foreach( $this->getNotificators() as $notificator ) 
		{
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;

			$notificator->delete( $object_it );
		}
	}
	
	public function executeEventsAfterBusinessTransaction( $object_it, $interface_name )
	{
		foreach( getSession()->getBuilders($interface_name) as $handler )
		{
			$handler->setObjectIt($object_it->object->getExact($object_it->getId()));
			
			if ( !$handler->readyToHandle() ) continue;
			
			$handler->process();
		}
		
		$object_it->moveFirst();
	}
}