<?php

class TaskTraceBaseIterator extends OrderedIterator
{
    function getDisplayNameReference()
    {
        return 'ObjectId';
    }
    
    function getDisplayName()
 	{
 		$object_it = $this->getRef(
 				$this->getDisplayNameReference(), 
 				$this->getDisplayNameReference() == 'Task'
						? getFactory()->getObject('Task')
						: null
		);

 		$uid = new ObjectUID;
 		
 		if ( $uid->hasUid($object_it) )
 		{
 			return $uid->getUidWithCaption($object_it);
 		}
 		else
 		{
 			return $object_it->getDisplayName();
 		}
 	}
    
 	function getTraceDisplayName()
 	{
 		$uid = new ObjectUID;
 		$object_it = $this->getObjectIt();
 		
 		return translate('Трассировка').': '.
 			$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
 	
	function getObjectIt()
	{
		$object = getFactory()->getObject( $this->get('ObjectClass') );
	
		if ( $this->get('ObjectId') == '' ) return $object->getEmptyIterator();
		
		return $object->getExact( $this->get('ObjectId') );
	}
}
