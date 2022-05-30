<?php
// PHPLOCKITOPT NOOBFUSCATE
// PHPLOCKITOPT NOENCODE

class ModelEventsManager
{
 	private $notificators = array();
 	private $delay = false;
 	private $delayedNotifications = array();
 	private $cascade = false;
 	private $counter = 0;
	
 	public function setCascade( $cascade = true ) {
 		$this->cascade = $cascade;
 	}

 	public function getCascade() {
 		return $this->cascade;
 	}

 	public function delayNotifications( $value = true ) {
 	    $this->delay = $value;
    }
 	
	function registerNotificator( $notificator_object ) {
		$this->notificators[get_class($notificator_object)] = $notificator_object;
	}
	
	function removeNotificator( $notificator_object )
	{
		if ( is_object($notificator_object) ) {
			unset($this->notificators[get_class($notificator_object)]);
		}
		else {
			unset($this->notificators[$notificator_object]);
		}
	}

    function removeNotificatorsExcept( $notificator_object )
    {
        foreach ( $this->notificators as $key => $notificator ) {
            if ( $notificator instanceof $notificator_object ) continue;
            unset($this->notificators[$key]);
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

	function getDelayKey( $notificator, $objectIt, $action ) {
 	    return ($this->counter++)
            . md5(get_class($notificator).get_class($objectIt).$objectIt->getId())
                . $action . ' - ' . get_class($notificator);
    }
	
 	function notify_object_add( $object_it, $data = array() ) 
 	{
		foreach( $this->getNotificators() as $notificator )
		{
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;

			if ( $this->delay ) {
			    $delayedObjectIt = $object_it->copy();

			    $notifcationKey = $this->getDelayKey($notificator, $delayedObjectIt, 'add');
			    if ( array_key_exists($notifcationKey, $this->delayedNotifications) ) continue;

			    $this->delayedNotifications[$notifcationKey] = function() use ($notificator, $data, $delayedObjectIt) {
                    $notificator->setRecordData( $data );
                    $notificator->add( $delayedObjectIt );
                };
            }
            else {
                $notificator->setRecordData( $data );
                $notificator->add( $object_it );
            }
		}
	}

 	function notify_object_modify( $prev_object_it, $object_it, $data = array() ) 
 	{
		foreach( $this->getNotificators() as $notificator ) {
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;

            if ( $this->delay ) {
                $delayedObjectIt = $prev_object_it->copy();
                $notifcationKey = $this->getDelayKey($notificator, $delayedObjectIt, 'modify');
                if ( array_key_exists($notifcationKey, $this->delayedNotifications) ) continue;

                $this->delayedNotifications[$notifcationKey] = function() use ($notificator, $data, $delayedObjectIt) {
                    $notificator->setRecordData( $data );
                    $notificator->modify(
                        $delayedObjectIt,
                        $delayedObjectIt->object
                            ->getRegistry()->QueryById($delayedObjectIt->getId())
                    );
                };
            }
            else {
                $notificator->setRecordData( $data );
                $notificator->modify( $prev_object_it, $object_it );
            }
		}
	}

 	function notify_object_delete( $object_it ) 
 	{
		foreach( $this->getNotificators() as $notificator ) {
			if ( !$this->notificationEnabled($object_it, $notificator) ) continue;

            if ( $this->delay ) {
                $delayedObjectIt = $object_it->copy();
                $notifcationKey = $this->getDelayKey($notificator, $delayedObjectIt, 'delete');
                if ( array_key_exists($notifcationKey, $this->delayedNotifications) ) continue;

                $this->delayedNotifications[$notifcationKey] = function() use ($notificator, $delayedObjectIt) {
                    $notificator->delete( $delayedObjectIt );
                };
            }
            else {
                $notificator->delete( $object_it );
            }
		}
	}
	
	public function executeEventsAfterBusinessTransaction( $object_it, $interface_name, $data = array() )
	{
        while( !$object_it->end() ) {
            foreach( getSession()->getBuilders($interface_name) as $handler ) {
                if ( !$this->notificationEnabled($object_it, $handler) ) continue;
                $handler->setObjectIt($object_it->copy());
                if ( !$handler->readyToHandle() ) continue;

                if ( $this->delay ) {
                    $delayedObjectIt = $object_it->copy();
                    $notifcationKey = $this->getDelayKey($handler, $delayedObjectIt, 'workflow');
                    if ( array_key_exists($notifcationKey, $this->delayedNotifications) ) continue;

                    $this->delayedNotifications[$notifcationKey] = function() use ($handler, $delayedObjectIt, $data) {
                        $handler->setObjectIt($delayedObjectIt->copy());
                        $handler->process( $data );
                    };
                }
                else {
                    $handler->process( $data );
                }
            }
            $object_it->moveNext();
        }
	}

	public function releaseNotifications()
    {
        $this->delayNotifications(false);
        foreach( $this->delayedNotifications as $key => $functor ) {
            $functor();
        }
        $this->delayedNotifications = array();
    }
}