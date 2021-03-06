<?php
include "PMCustomAttributeIterator.php";
include "predicates/CustomAttributeEntityPredicate.php";
include "predicates/CustomAttributeValuePredicate.php";
include "predicates/CustomAttributeObjectPredicate.php";
include_once "persisters/CustomAttributesPersister.php";

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
        $this->setAttributeDescription('Groups', text(2645));
 		
 		foreach( array('ObjectKind', 'AttributeTypeClassName', 'Capacity') as $field ) {
			$this->addAttributeGroup($field, 'system');
		}
        foreach( array('AttributeType', 'EntityReferenceName','OrderNum', 'Groups', 'Description') as $field ) {
            $this->addAttributeGroup($field, 'additional');
        }
        foreach( array('ReferenceName') as $field ) {
            $this->addAttributeGroup($field, 'alternative-key');
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

    function getPage()
    {
        return getSession()->getApplicationUrl($this).'project/dicts/PMCustomAttribute?';
    }

 	function getEntityDisplayName( $ref_name, $kind )
 	{
 		$class_name = getFactory()->getClass($ref_name);
 		if ( !class_exists($class_name) ) return '';
 		
		$ref = getFactory()->getObject($class_name);
		if ( !is_object($ref) ) return '';
		
 		if ( $kind == '' ) {
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

 	function getEntityClasses($object)
    {
        return array_merge(
            array(
                strtolower(get_class($object))
            ),
            array_values(
                array_diff(
                    array_map(
                        function($class) {
                            return strtolower($class);
                        },
                        class_parents($object)
                    ),
                    array(
                        'metaobject', 'metaobjectstatable', 'storedobjectdb', 'abstractobject'
                    )
                )
            )
        );
    }

 	function getByEntity( $object )
 	{
 		$settings = array (
            new FilterAttributePredicate('EntityReferenceName', $this->getEntityClasses($object)),
            new FilterVpdPredicate()
		);
 		return $this->getRegistry()->Query($settings);
 	}
 	
 	function getByAttribute( $object, $attribue )
 	{
        $settings = array (
            new FilterAttributePredicate('EntityReferenceName', $this->getEntityClasses($object)),
            new FilterAttributePredicate('ReferenceName', $attribue),
            new FilterVpdPredicate()
        );
        return $this->getRegistry()->Query($settings);
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
 		if ( $object_it->count() > 0 ) return -1;

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
 				if ( $object_it->get($parms['ReferenceName']) == '' ) {
 					$object_it->moveNext(); continue;
 				}
 				if ( $parms['ReferenceName'] != 'UID' ) {
                    $value->add_parms( array (
                        'CustomAttribute' => $result,
                        'ObjectId' => $object_it->getId(),
                        $type_it->getValueColumn() => $object_it->getHtmlDecoded($parms['ReferenceName'])
                    ));
                }
				$object_it->moveNext();
 			}

			if ( $type_it->get('ReferenceName') == 'computed' ) {
				$this->rebuildComputedAttributes($result, $parms);
			}
		}
 		
 		return $result;
 	}

	function modify_parms($id, $parms)
	{
		$was_parms = $this->getExact($id)->getData();

		$result = parent::modify_parms($id, $parms);

		if ( $parms['ReferenceName'] == 'UID' && $was_parms['DefaultValue'] != $parms['DefaultValue'] ) {
			$type_it = getFactory()->getObject('CustomAttributeType')->getExact($parms['AttributeType']);
			if ( $type_it->get('ReferenceName') == 'computed' ) {
				$this->rebuildComputedAttributes($id, $parms);
			}
		}

		return $result;
	}

	function delete( $id, $record_version = ''  )
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

	protected function rebuildComputedAttributes( $id, $parms )
	{
		$registry = $this->getExact($id)->getEntityRegistry();
		if ( !is_object($registry) ) return;

        $registry->getObject()->setAttributeDefault($parms['ReferenceName'], $parms['DefaultValue']);
        $registry->getObject()->setNotificationEnabled(false);

		$persister = new CustomAttributesPersister();
		$persister->setObject($registry->getObject());
		$registry->setPersisters(array($persister));

		$object_it = $registry->getAll();
		while( !$object_it->end() ) {
			$data = $object_it->getData();

            unset($data['Caption']);
            unset($data['Description']);
            unset($data['Content']);
			unset($data['RecordCreated']);
			unset($data['RecordVersion']);
            unset($data['UID']);

			$registry->Store($object_it, $data);
			$object_it->moveNext();
		}
	}
}