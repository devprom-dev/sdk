<?php

include_once "SystemTriggersBase.php";

class CacheResetTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		$references = getFactory()->getModelReferenceRegistry()->getBackwardReferences($object_it->object);

		foreach( $references as $class_name )
		{
			$class_name = getFactory()->getClass($class_name);
			
			if ( !class_exists($class_name, false) ) continue;
			
			$ref = getFactory()->getObject($class_name);
			
			if ( is_a($ref, 'MetaobjectCacheable') ) $ref->resetCache();
		}
	}
}
 