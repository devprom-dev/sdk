<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "SharedObjectRegistry.php";

class SharedObjectSet extends PMObjectCacheable
{
 	public function __construct() 
 	{
 		parent::__construct('entity', new SharedObjectRegistry($this));
 	}
 	
 	function hasObject( $object )
 	{
 	    $object_it = $this->getExact(strtolower(get_class($object)));

 	    return $object_it->getId() != '';
 	}
 	
 	function sharedInProject( $object, $project_it )
 	{
		if ( $project_it->getId() == '' ) throw new Exception('Correct Project iterator should be given');

		$object_it = $this->getExact(strtolower(get_class($object)));
 	    
 	    if ( $object_it->getId() == '' ) return false;
 	    
 	 	$builders = getSession()->getBuilders('SharedObjectsBuilder'); 
 	    
 	    foreach( $builders as $builder )
 	    {
 	        if ( $builder->getGroup() == $object_it->get('Category') )
 	        {
 	            return $builder->checkSharedInProject( $project_it );
 	        }
 	    }

 	    return true;
 	}
 	
 	function moveToObject( $object )
 	{
 	    $it = $this->getExact(strtolower(get_class($object)));
 	    
 	    return $it->getId() != '' ? $it : $this->getEmptyIterator();
 	}
}
