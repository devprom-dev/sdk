<?php

class WatcherIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		if ( $this->get('SystemUser') == '' ) {
 			return $this->get('Email');
 		}
 		else
 		{
			if ( !is_numeric($this->get('SystemUser')) ) {
				return $this->get('SystemUser');
			}
			else {
				$user_it = $this->getRef('SystemUser');
				return $user_it->getDisplayName();
			}
 		}
 	}

	function getAnchorIt()
	{
		$object = getFactory()->getObject($this->get('ObjectClass'));
		if ( !is_object($object) ) return $this->object->getEmptyIterator();
		
		return $object->getExact( $this->get('ObjectId') );
	}
}
