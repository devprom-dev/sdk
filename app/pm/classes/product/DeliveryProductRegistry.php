<?php

class DeliveryProductRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
    	$data = array(
    			array ('entityId' => 2, 'ReferenceName' => 'Milestone', 'Caption' => translate('Веха'))
    	);
    	$last_entity_id = 2;
    	
    	// features
    	$types = array();
    	$type_it = getFactory()->getObject('FeatureType')->getRegistry()->Query(
    			array (
    					new FilterVpdPredicate()
    			)
    	);

    	while( !$type_it->end() )
    	{
    		if ( $type_it->get('ReferenceName') == '' ) {
    			$type_it->moveNext();
    			continue;
    		}
    		
    		$types[$type_it->get('ReferenceName')] = $type_it->getDisplayName(); 
    		$type_it->moveNext();
    	}
    	foreach($types as $ref_name => $type_name )
    	{
    		$data[] = array ('entityId' => ++$last_entity_id, 'ReferenceName' => 'Feature'.$ref_name, 'Caption' => $type_name);
    	}
		$data[] = array ('entityId' => ++$last_entity_id, 'ReferenceName' => 'Feature', 'Caption' => text('ee226'));

		// issues
        $types = array();
    	$type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
    			array (
    					new FilterVpdPredicate()
    			)
    	);
    	while( !$type_it->end() )
    	{
    		$types[$type_it->get('ReferenceName')] = $type_it->getDisplayName(); 
    		$type_it->moveNext();
    	}
    	
    	foreach($types as $ref_name => $type_name )
    	{
    		$data[] = array ('entityId' => ++$last_entity_id, 'ReferenceName' => 'Request'.$ref_name, 'Caption' => $type_name);
    	}
    	$data[] = array ('entityId' => ++$last_entity_id, 'ReferenceName' => 'Request', 'Caption' => translate('Пожелание'));
    	
    	return $this->createIterator( $data );
    }
}