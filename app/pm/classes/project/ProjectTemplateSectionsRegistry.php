<?php

class ProjectTemplateSectionsRegistry extends ObjectRegistrySQL
{
 	private $objects = array();
 	private $terminalObjects = array();
 	
 	function addSection( &$object, $ref_name = '', $items = array(), $visible = true, $description = '' )
 	{
 		$ref_name = $ref_name == '' ? $object->getClassName() : $ref_name;
 		
 		$this->objects[$ref_name] = array( 
 			'ReferenceName' => $ref_name,
 			'items' => count($items) < 1 ? array( $object ) : $items,
 			'IsVisible' => $visible ? 'Y' : 'N',
 			'Description' => $description
 		);
 	}
 	
 	function addSectionItem( $ref_name, $object ) {
        $this->objects[$ref_name]['items'][] = $object;
 	}

    function addTerminalSectionItem( $ref_name, $object ) {
        $this->terminalObjects[$ref_name]['items'][] = $object;
    }

    function addSectionItemBefore( $className, $ref_name, $object )
    {
        $foundKey = 0;
        foreach( $this->objects[$ref_name]['items'] as $key => $object ) {
            if ( is_a($object, $className) ) {
                $foundKey = $key;
                break;
            }
        }
        $tail = array_splice($this->objects[$ref_name]['items'], $foundKey,
            count($this->objects[$ref_name]['items']), array($object) );
        $this->objects[$ref_name]['items'] = array_merge($this->objects[$ref_name]['items'], $tail);
    }

    function ushiftSectionItem( $ref_name, $object )
    {
        array_unshift( $this->objects[$ref_name]['items'], $object );
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
 		    $subItems = $object['items'];
 		    if ( is_array($this->terminalObjects[$key]['items']) ) {
                $subItems = array_merge($subItems, $this->terminalObjects[$key]['items']);
            }
 			$item = array (
 				'entityId' => get_class($object['object']),
 				'ReferenceName' => $object['ReferenceName'],
 				'items' => $subItems,
 				'IsVisible' => $object['IsVisible'],
 				'Description' => $object['Description']
 			);
 			
 			if ( in_array($key, array('ProjectArtefacts','Knowledgebase')) ) {
 				$latest[] = $item;
 			}
 			else {
 				$data[] = $item;
 			}
 		}
 		
 		return $this->createIterator( array_values(array_merge($data, $latest)) );
	}
}