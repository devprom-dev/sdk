<?php

class ProjectTemplateSectionsRegistry extends ObjectRegistrySQL
{
 	private $objects = array();
 	
 	function addSection( &$object, $ref_name = '', $items = array(), $visible = true, $description = '' )
 	{
 		$ref_name = $ref_name == '' ? $object->getClassName() : $ref_name;
 		
 		$this->objects[$ref_name] = array( 
 			'object' => $object,
 			'ReferenceName' => $ref_name,
 			'items' => count($items) < 1 ? array( $object ) : $items,
 			'IsVisible' => $visible ? 'Y' : 'N',
 			'Description' => $description
 		);
 	}
 	
 	function addSectionItem( $ref_name, $object )
 	{
 		array_push( $this->objects[$ref_name]['items'], $object );
 	}

 	function setSectionVisible( $ref_name, $b_visible = true )
 	{
 		$this->objects[$ref_name]['IsVisible'] = $b_visible ? 'Y' : 'N';
 	}
 	
 	function createSQLIterator( $sql )
 	{
 	    $builders = getSession()->getBuilders('ProjectTemplateSectionsRegistryBuilder'); 
 	    
 	    foreach( $builders as $builder )
 	    {
 	        $builder->build( $this );
 	    }

 		$data = array();
 		
 		$latest = array();
 		
 		foreach( $this->objects as $key => $object )
 		{
 			$item = array (
 				'entityId' => get_class($object['object']),
 				'ReferenceName' => $object['ReferenceName'],
 				'object' => $object['object'],
 				'items' => $object['items'],
 				'IsVisible' => $object['IsVisible'],
 				'Description' => $object['Description']
 			);
 			
 			if ( $key == 'ProjectArtefacts' )
 			{
 				$latest[] = $item;
 			}
 			else
 			{
 				$data[] = $item;
 			}
 		}
 		
 		return $this->createIterator( array_values(array_merge($data, $latest)) );
	}
}