<?php

class RequestTraceBaseIterator extends OrderedIterator
{
    function getDisplayNameReference() {
        return 'ObjectId';
    }

 	function getDisplayName()
 	{
 		$object_it = $this->getRef($this->getDisplayNameReference());

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
 		$object_it = $this->getObjectIt();
 		
 		$uid = new ObjectUID;
 		if ( $uid->hasUid($object_it) )
 		{
	 		return translate('Трассировка').': '.
	 			$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 		}
 		else
 		{
	 		return translate('Трассировка').': '.$object_it->getDisplayName();
 		}
 	}

 	function getBacktraceDisplayName()
 	{
	 	$uid = new ObjectUID;
	 	$request_it = $this->getRef('ChangeRequest');
	
 		return translate('Трассировка').': '.
 			$uid->getObjectUid($request_it).' '.$request_it->getDisplayName();
 	}

	function getObjectIt()
	{
		global $model_factory;
		
		$class_name = $model_factory->getClass($this->get('ObjectClass'));
		
		if ( !class_exists($class_name) ) return $this->object->getEmptyIterator();
		 
		$object = $model_factory->getObject( $class_name );

		if ( $this->get('ObjectId') == '' ) return $object->getEmptyIterator(); 
		
		return $object->getExact( $this->get('ObjectId') );
	}

	function getFullObjectIt()
	{
		global $model_factory;
		
		$object = $model_factory->getObject( $this->get('ObjectClass') );
		
		return count($this->fieldToArray('ObjectId')) > 0 ? 
		    $object->getExact($this->fieldToArray('ObjectId')) : $object->getEmptyIterator();
	}
}
