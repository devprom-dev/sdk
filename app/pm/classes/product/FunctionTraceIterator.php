<?php

class FunctionTraceIterator extends OrderedIterator
{
    function getDisplayNameReference()
    {
        return 'ObjectId';
    }
    
 	function getDisplayName()
 	{
 		$object_it = $this->getRef($this->getDisplayNameReference());

 		$uid = new ObjectUID;
 		
 		if ( $uid->hasUid($object_it) )
 		{
 			return $uid->getUidWithCaption($object_it, 50);
 		}
 		else
 		{
 			return $object_it->getDisplayName();
 		}
 	}
 	
	function getObjectIt()
	{
		global $model_factory;
		
		$object = $model_factory->getObject( $this->get('ObjectClass') );
		
		if( $this->get('ObjectId') == '' ) return $object->getEmptyIterator();

		return $object->getExact( $this->get('ObjectId') );
	}

 	function getTraceDisplayName()
 	{
 		$object_it = $this->getObjectIt();
 		
 		$uid = new ObjectUID;
 		
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
 	
 	function getBacktraceDisplayName()
 	{
 		$object_it = $this->getRef( 'Feature' );
 		
 		$uid = new ObjectUID;
 		
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
}