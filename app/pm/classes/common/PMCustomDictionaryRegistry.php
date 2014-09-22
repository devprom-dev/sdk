<?php

class PMCustomDictionaryRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	    global $model_factory;
 	    
 	    $filters = $this->getObject()->getFilters();
 	    
 	    if ( count($filters) < 1 ) return $this->createIterator(array());
 	    
 	    $parts = preg_split('/,/', $filters[0]->getValue());
 	    
 		$custom = $model_factory->getObject('pm_CustomAttribute');

 		$custom_it = $custom->getByAttribute( $model_factory->getObject($parts[0]), $parts[1] );

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