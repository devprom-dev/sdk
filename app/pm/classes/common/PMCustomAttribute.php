<?php

include "PMCustomAttributeIterator.php";
include "predicates/CustomAttributeEntityPredicate.php";
include "predicates/CustomAttributeValuePredicate.php";

class PMCustomAttribute extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_CustomAttribute');
 		
 		$this->setSortDefault( array( 
 		    new SortAttributeClause('EntityReferenceName'),
 		    new SortAttributeClause('OrderNum')
 		));
 		
 		$this->setAttributeType('AttributeType', 'REF_CustomAttributeTypeId');
 		
 		foreach( array('ValueRange', 'IsVisible', 'IsRequired', 'IsUnique', 'ObjectKind', 'AttributeTypeClassName', 'Capacity') as $field )
		{
			$this->addAttributeGroup($field, 'system');
		}
 	}
 	
 	function getDisplayName()
 	{
 		return translate('Атрибуты');
 	}
 	
 	function createIterator() 
 	{
 		return new PMCustomAttributeIterator( $this );
 	}
 	
 	function getEntityDisplayName( $ref_name, $kind )
 	{
 		$class_name = getFactory()->getClass($ref_name);
 		
 		if ( $class_name == '' ) return '';
 		
		$ref = getFactory()->getObject($class_name);
		
		if ( !is_object($ref) ) return '';
		
 		if ( $kind == '' )
 		{
			return $ref->getDisplayName();
 		}
 		else
 		{
 			switch ( $ref->getEntityRefName() )
 			{
 				case 'pm_ChangeRequest':
 					$type_it = getFactory()->getObject('pm_IssueType')->getByRef('ReferenceName', $kind);
 					break;
 					
 				case 'pm_Task':
 					$type_it = getFactory()->getObject('pm_TaskType')->getByRef('ReferenceName', $kind);
 					return $ref->getDisplayName().': '.$type_it->getDisplayName();

 				case 'WikiPage':
 					$type_it = getFactory()->getObject('WikiPageType')->getByRef('ReferenceName', $kind);
 					break;
 					
 				default:
 					return '';
 			}
 			
 			return $type_it->getDisplayName();
 		}
 	}
 	
 	function getByEntity( $object )
 	{
 		$settings = array (
				new FilterAttributePredicate('EntityReferenceName', strtolower(get_class($object))),
		);
 		
 		if ( count($object->getVpds()) > 0 )
 		{
 			$settings[] = new FilterBaseVpdPredicate();
 		}
 	 
 		return $this->getRegistry()->Query($settings);
 	}
 	
 	function getByAttribute( $object, $attribue )
 	{
 	    return $this->getByRefArray( array (
 	            'LCASE(EntityReferenceName)' => strtolower(get_class($object)),
 	            'LCASE(ReferenceName)' => strtolower($attribue)
 	    ));
 	}
 	
 	function add_parms( $parms )
 	{
 		// check for uniqueness
 		$object_it = $this->getByRefArray( 
	 				array (
			 			'LCASE(EntityReferenceName)' => strtolower($parms['EntityReferenceName']),
			 			'LCASE(ReferenceName)' => strtolower($parms['ReferenceName'])
	 				)
 			);
 		
 		if ( $object_it->count() > 0 )
 		{
 			return -1;
 		}
 		
 		$object = getFactory()->getObject($parms['EntityReferenceName']);
 		
 		$move_value = $object->getAttributeType($parms['ReferenceName']) != '';
 		
 		if ( $move_value ) $object_it = $object->getAll();
 		
 		$result = parent::add_parms( $parms );
 		
 		if ( $result > 0 && is_object($object_it) )
 		{
 			$type_it = $this->getAttributeObject('AttributeType')->getExact($parms['AttributeType']);
 			
 			$value = getFactory()->getObject('pm_AttributeValue');
 			
 			while ( !$object_it->end() )
 			{
 				if ( $object_it->get($parms['ReferenceName']) == '' )
 				{
 					$object_it->moveNext(); continue;
 				}
 				
				$value->add_parms( array (
					'CustomAttribute' => $result,
					'ObjectId' => $object_it->getId(),
					$type_it->getValueColumn() => $object_it->getHtmlDecoded($parms['ReferenceName'])
				));
				
				$object_it->moveNext();
 			}
 		}
 		
 		return $result;
 	}
 	
 	function delete( $id )
 	{
 	    global $model_factory;
 	    
 	    $object_it = $this->getExact( $id );
 	    
 	    $reference = $model_factory->getObject($object_it->get('EntityReferenceName'));
 	    
 	    // check if custom attribute overwrites real one
 	    $stored_attribute = false;
 	    
 	    $attribute_it = $reference->getEntity()->getAttributes()->getAll();
 	    
        while( !$attribute_it->end() )
        {
            if ( $attribute_it->get('ReferenceName') == $object_it->get('ReferenceName') )
            {
                $stored_attribute = true;
                break;
            }
            
            $attribute_it->moveNext();
        }
 	    
        if ( $stored_attribute )
        {
            // copy value from custom attribute to the real one
            $reference->setAttributeStored($object_it->get('ReferenceName'), true);
            
            $reference->removeNotificator( 'EmailNotificator' );
            $reference->removeNotificator( 'ChangeLogNotificator' );
            
            $reference->resetPersisters();
            
            $value = $model_factory->getObject('pm_AttributeValue');
            
            $value_it = $value->getByRefArray( array(
                    'CustomAttribute' => $object_it->getId()
            ));
            
            if ( $value_it->getId() > 0 )
            {
                $reference_it = $reference->getExact($value_it->get('ObjectId'));
                
                if ( $reference_it->getId() != '' )
                {
                    while ( !$value_it->end() )
                    {
                        $data = $value_it->getHtmlDecoded($object_it->getRef('AttributeType')->getValueColumn());
                    
                        $reference->modify_parms( $reference_it->getId(),
                        		array (
		                                $object_it->get('ReferenceName') => $data
		                        )
                        );
                    
                        $value_it->moveNext();
                    }
                }
            }
        }
 	    
 	    return parent::delete( $id );
 	}
}