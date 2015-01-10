<?php

include "WatcherIterator.php";
include "WatcherRegistry.php";
include_once "persisters/WatchersPersister.php";

class Watcher extends Metaobject
{
 	var $object_it, $size;
 	
 	function Watcher( $object_it = null ) 
 	{
 		parent::Metaobject('pm_Watcher', new WatcherRegistry($this));
 		
 		$this->setAttributeType('SystemUser', 'REF_ProjectUserId');
 		
		$this->object_it = $object_it;
 	}
 	
 	function createIterator() 
 	{
 		return new WatcherIterator( $this );
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function getAll()
 	{
 		if ( !is_object($this->object_it) )
 		{
 			return $this->getByRef('ObjectId', "-1");
 		}
 		
 		return parent::getAll();
 	}
 	
 	function getWatched( $user_it )
 	{
 		return $this->getByRefArray(
 			array ( 
 				'SystemUser' => $user_it->getId()
 				)
 			);
 	}
 	
 	function watchedByEmail( $email )
 	{
 		return $this->getByRefArray(
 			array ( 
 				'LCASE(Email)' => strtolower($email)
 				)
 			);
 	}

 	function getAllWatched( $user_it )
 	{
 		return $this->getByRefArray(
 			array (	'SystemUser' => $user_it->getId() )
 			);
 	}

	function getExact( $object_id )
	{
		$it = parent::getExact( $object_id );
		
		if ( $it->count() < 1 )
		{
			return $it;
		}
		
		$this->object_it = getFactory()->getObject($it->get('ObjectClass'))->getExact($it->get('ObjectId'));

		$it->object = $this;
		
		return $it; 
	}
	
	function IsAttributeRequired( $attr_name )
	{
		switch ( $attr_name )
		{
			case 'Email':
				return false;
			case 'SystemUser':
				return true;
		}
	}
	
	function getAnchorIt()
	{
		return $this->object_it;
	}
	
	function add_parms( $parms )
	{
	    if ( is_object($this->object_it) )
	    {
	        if ( !array_key_exists('ObjectId', $parms) )
	        {
	            $parms['ObjectId'] = $this->object_it->getId();
	        }

	        if ( !array_key_exists('ObjectClass', $parms) )
	        {
	            $parms['ObjectClass'] = strtolower(get_class($this->object_it->object));
	        }
	    }
	    
	    return parent::add_parms( $parms );
	}
}