<?php

class PMCustomDictionaryRegistry extends ObjectRegistrySQL
{
	function __construct( $object )
	{
		parent::__construct( $object );
	}

	function getFilters()
	{
		return array_merge (
				parent::getFilters(),
				array (
						new FilterPredicate(get_class($this->getObject()->getAttributeObject()).",".$this->getObject()->getAttribute())
				)
		);
	}
	
 	function createSQLIterator( $sql )
 	{
 		$custom_it = getFactory()->getObject('pm_CustomAttribute')->
 				getByAttribute( $this->getObject()->getAttributeObject(), $this->getObject()->getAttribute() );

 		$data = array();

 		foreach( $custom_it->toDictionary() as $key => $value )
 		{	
 		    $data[] = array (
 		        'entityId' => $key,
 		        'Caption' => $value
 		    );
 		}
 		
 		return $this->createIterator( $data );
 	}
}