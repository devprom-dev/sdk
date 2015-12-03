<?php

include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPasswordPersister.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";

class CustomAttributesPersister extends ObjectSQLPersister
{
 	var $attrs;
 	
 	protected function getAttributesInfo()
 	{
 		if ( is_array( $this->attrs) ) return $this->attrs;
 		
 		$this->attrs = array();
 		
 		$object = $this->getObject();
 		
 		$attr = getFactory()->getObject('pm_CustomAttribute');
 		
 		$attr_it = $attr->getByEntity( $object );
 		
 		while ( !$attr_it->end() )
 		{
 			$this->attrs[$attr_it->getId()] = array (
			    'id' => $attr_it->getId(),
 				'name' => $attr_it->get('ReferenceName'),
 				'type' => $attr_it->get('AttributeType'),
				'default' => $attr_it->getHtmlDecoded('DefaultValue'),
 			);
		 
 			$attr_it->moveNext();
 		}

 		return $this->attrs;
 	}
 	
 	function add( $object_id, $parms )
 	{
		$this->set($object_id, $parms);
 	}

 	function modify( $object_id, $parms )
 	{
		$this->set($object_id, $parms);
 	}
 	
 	protected function set( $object_id, $parms )
 	{
 	 	$attributes = $this->getAttributesInfo();
 		
 		$ids = array();
 		
 		foreach( $attributes as $attribute ) $ids[] = $attribute['id']; 
 		
 		$value = getFactory()->getObject('pm_AttributeValue');
 		
 		$value_it = $value->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('CustomAttribute', $ids),
 						new FilterAttributePredicate('ObjectId', $object_id),
 						new ObjectSQLPasswordPersister(),
 						new SortAttributeClause('CustomAttribute')
 				)
 		);
 		
 		foreach( $attributes as $attr_id => $attr )
 		{
 			$value_it->moveTo('CustomAttribute', $attr['id']);
 			
 			if ( $value_it->getId() == '' )
 			{
 				// append
 				$value_parms = array(
 					'CustomAttribute' => $attr['id'],
 					'ObjectId' => $object_id
 				);
 				
 				$this->setValueParms( $attr, $parms, $value_parms );
 				
 				$value_it->object->add_parms( $value_parms );
 			}
 			else
 			{
 				// update
 				$value_column = getFactory()->getObject('pm_CustomAttribute')->
 						getAttributeObject('AttributeType')->getExact($attr['type'])->getValueColumn();
	 			
	 			$value_parms = array(
	 				$value_column => $value_it->getHtmlDecoded( $value_column )
	 			);

	 			$this->setValueParms( $attr, $parms, $value_parms );
 				
	 			$value->modify_parms($value_it->getId(), $value_parms);
 			}
 		}
 	}

 	function delete( $object_id )
 	{
 		$attributes = $this->getAttributesInfo();

 		$ids = array();
 		foreach( $attributes as $attribute ) $ids[] = $attribute['id'];
 		
 		$value = getFactory()->getObject('pm_AttributeValue');
 		$value_it = $value->getByRefArray(
 			array( 'CustomAttribute' => $ids,
 				   'ObjectId' => $object_id ) 
 		);
 		
 		while( !$value_it->end() ) 
 		{
			$value->delete($value_it->getId());
 			$value_it->moveNext();
 		}
 	}
 	
 	function setValueParms( $attribute, $parms, & $value_parms )
 	{
		$value_column = getFactory()->getObject('pm_CustomAttribute')->
				getAttributeObject('AttributeType')->getExact($attribute['type'])->getValueColumn();
 		
 		$object = $this->getObject();

 		if ( !array_key_exists( $attribute['name'], $parms ) )
 		{
 			$parms[$attribute['name']] = $value_parms[$value_column];
 		}
 		else
 		{
 			$parms[$attribute['name']] = html_entity_decode($parms[$attribute['name']], ENT_QUOTES | ENT_HTML401, APP_ENCODING);
 		}
 		
 		$use_default = $object->IsAttributeRequired($attribute['name'])
 			&& $parms[$attribute['name']] == '' && $value_parms[$value_column] == '' 
 			&& $attribute['default'] != '';
 		 
 		$value_parms[$value_column] = $use_default ? $attribute['default'] : $parms[$attribute['name']];
 		
 	 	if ( $value_column == 'IntegerValue' && !is_numeric($parms[$attribute['name']]) )
 		{
 			$value_parms[$value_column] = '';
 		}
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
 		$attributes = $this->getAttributesInfo();
 		
 		$object = $this->getObject();
 		
		$algorithm = defined('MYSQL_ENCRYPTION_ALGORITHM') ?
			  MYSQL_ENCRYPTION_ALGORITHM : 'DES'; 
 		
		// merge custom attributes with the same ref name
		$attribute_ids = array();
		
		$attribute_data = array();
		
		foreach( $attributes as $attr_id => $attr )
 		{
 		    $ref_name = $attr['name'];
 		    
 		    $attribute_data[$ref_name] = $attr;
 		    $attribute_ids[$ref_name][] = $attr['id'];
 		}

 		foreach( $attribute_data as $ref_name => $attr )
 		{
			if ( !preg_match("/^[a-zA-Z][a-zA-Z0-9\_]+$/i", $attr['name']) ) continue;
 			
			$type_it = getFactory()->getObject('pm_CustomAttribute')
				->getAttributeObject('AttributeType')->getExact($attr['type']);
			
			$value_column = $type_it->getValueColumn();
 			
 			$objectPK = $alias.$object->getClassName().'Id';
 
 			if ( $type_it->get('ReferenceName') == 'password' )
 			{
				switch ( $algorithm )
				{
					case 'AES':
						$column = "AES_DECRYPT("."cav.".$value_column.", '".INSTALLATION_UID."') ";
						break;
		
					default:
						$column = "DES_DECRYPT("."cav.".$value_column.", '".INSTALLATION_UID."') ";
				}
 			}
 			else
 			{
 				$column = "cav.".$value_column;
 			}			

 			array_push( $columns, 
 				"(SELECT ".$column." FROM pm_AttributeValue cav ".
 				"  WHERE cav.ObjectId = ".$objectPK.
 				"    AND cav.CustomAttribute IN (".join(',',$attribute_ids[$ref_name]).") LIMIT 1) `".trim($attr['name'])."` "
 			);
 		}
 		
 		return $columns;
 	}
} 