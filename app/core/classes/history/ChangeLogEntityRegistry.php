<?php

class ChangeLogEntityRegistry extends ObjectRegistrySQL
{
    protected $names = array();
	
 	function add( $entity_reference_name )
 	{
 	    $this->names[] = $entity_reference_name;
 	}
    
    function createSQLIterator( $sql )
 	{
 	 	foreach( getSession()->getBuilders('ChangeLogEntitiesBuilder') as $builder )
 	    {
 	        $builder->build( $this );
 	    }
 	    
 	    $data = array();
 	    
 	    foreach( $this->names as $class_name )
 	    {
 	        if ( $class_name == '' ) continue;
            $class_name = getFactory()->getClass($class_name);
 	        if ( !class_exists($class_name) ) continue;

 	        $object = getFactory()->getObject($class_name);
 	        $data[] = array(
                'entityId' => $class_name,
                'ReferenceName' => $class_name,
                'ClassName' => strtolower(get_class($object)) == 'metaobject'
                    ? strtolower($object->getEntityRefName()) : strtolower(get_class($object)),
                'Caption' => translate($object->getDisplayName())
 	        );
 	    }

 	    usort( $data, function($left, $right) {
 	        return strcmp(translate($left['Caption']), translate($right['Caption']));
 	    });

 	    return $this->createIterator( $data );
 	}

	public function getLimit() {
		return 256;
	}
}
