<?php

class WatcherIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		if ( $this->get('SystemUser') == '' )
 		{
 			return $this->get('Email');
 		}
 		else
 		{
 			$user_it = $this->getRef('SystemUser');
 			return $user_it->getDisplayName();
 		}
 	}

	function getAnchorIt()
	{
		global $model_factory;

		$object = $model_factory->getObject($this->get('ObjectClass'));
		
		if ( !is_object($object) ) return null;
		
		return $object->getExact( $this->get('ObjectId') );
	}
}
