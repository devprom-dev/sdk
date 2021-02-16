<?php

class PMCustomDictionaryRegistry extends ObjectRegistrySQL
{
	function __construct( $object ) {
		parent::__construct( $object );
	}

 	function createSQLIterator( $sql )
 	{
        $attribute = getFactory()->getObject('pm_CustomAttribute');
        $attribute->setVpdContext($this->getObject()->getVpdContext());

 		$custom_it = $attribute->getByAttribute(
 		    $this->getObject()->getObjectForAttribute(), $this->getObject()->getAttribute()
        );

 		$data = array();
 		foreach( $custom_it->toDictionary() as $key => $value ) {
 		    $data[] = array (
 		        'entityId' => $key,
 		        'Caption' => $value
 		    );
 		}
 		
 		return $this->createIterator( $data );
 	}
}