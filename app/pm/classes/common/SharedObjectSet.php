<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "SharedObjectRegistry.php";

class SharedObjectSet extends PMObjectCacheable
{
 	public function __construct() {
 		parent::__construct('entity', new SharedObjectRegistry($this));
 	}
 	
 	function hasObject( $object ) {
 	    return $this->getExact(strtolower(get_class($object)))->getId() != '';
 	}
 	
 	function sharedInProject( $object, $project_it )
 	{
		if ( $project_it->getId() == '' ) {
		    throw new Exception('Correct Project iterator should be given');
        }

		$object_it = $this->getExact(strtolower(get_class($object)));
 	    if ( $object_it->getId() == '' ) return false;
 	    
 	    foreach( getSession()->getBuilders('SharedObjectsBuilder') as $builder ) {
 	        if ( $builder->getGroup() == $object_it->get('Category') ) {
 	            return $builder->checkSharedInProject( $project_it );
 	        }
 	    }

 	    return true;
 	}
}
