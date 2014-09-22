<?php

include "WatcherIterator.php";
include_once "persisters/WatchersPersister.php";

class Watcher extends Metaobject
{
 	var $object_it, $size;
 	
 	function Watcher( $object_it = null, $size = 1 ) 
 	{
 		parent::Metaobject('pm_Watcher');
 		
 		$this->setAttributeType('SystemUser', 'REF_ProjectUserId');
 		
		$this->object_it = $object_it;
		$this->size = $size;
 	}
 	
 	function createIterator() 
 	{
 		return new WatcherIterator( $this );
 	}
 	
 	function getAll() 
 	{
 		if ( !isset($this->object_it) )
 		{
	 		return $this->getByRefArray(
				array( 'ObjectId' => '-1' ) );
 		}
 		else
 		{
 			return $this->getRegistry()->Query(
 					array_merge( $this->getFilters(),
							array (
	 							new FilterAttributePredicate('ObjectId', $this->object_it->idsToArray()),
	 							new FilterAttributePredicate('ObjectClass', 
	 									array (
	 											strtolower($this->object_it->object->getClassName()),
	 											strtolower(get_class($this->object_it->object))
	 									)
	 							),
 								new SortAttributeClause('ObjectId'),
 								new SortAttributeClause('RecordCreated')
							)						
 					)
 			);
 		}
 	}
 	
 	function getWatched( $user_it )
 	{
 		return $this->getByRefArray(
 			array ( 
				'LCASE(ObjectClass)' => 
 					array ( strtolower($this->object_it->object->getClassName()),
 							strtolower(get_class($this->object_it->object))),
 				'ObjectId' => $this->object_it->getId(),
 				'SystemUser' => $user_it->getId()
 				)
 			);
 	}
 	
 	function watchedByEmail( $email )
 	{
 		return $this->getByRefArray(
 			array ( 
				'LCASE(ObjectClass)' => 
 					array ( strtolower($this->object_it->object->getClassName()),
 							strtolower(get_class($this->object_it->object))),
 				'ObjectId' => $this->object_it->getId(),
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