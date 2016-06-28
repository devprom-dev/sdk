<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPasswordPersister.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";

class CustomAttributesPersister extends ObjectSQLPersister
{
 	private $attrs = array();
	private $references = array();

	function setObject( $object )
	{
		parent::setObject($object);
		$this->getAttributesInfo();
		$this->getReferenceNames();
	}

 	protected function getAttributesInfo()
 	{
 		if ( count($this->attrs) > 0 ) return $this->attrs;

 		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($this->getObject());
 		while ( !$attr_it->end() )
		{
 			$this->attrs[$attr_it->getId()] = array (
			    'id' => $attr_it->getId(),
 				'name' => $attr_it->get('ReferenceName'),
 				'type' => $attr_it->getRef('AttributeType')->getData(),
				'default' => $attr_it->getHtmlDecoded('DefaultValue')
 			);
 			$attr_it->moveNext();
 		}

 		return $this->attrs;
 	}

	protected function getReferenceNames()
	{
		if (count($this->references) > 0) return $this->references;

		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
			array(
				new FilterAttributePredicate('EntityReferenceName', strtolower(get_class($this->getObject())))
			)
		);
		while( !$attr_it->end() ) {
			$this->references[$attr_it->get('ReferenceName')][] = $attr_it->getId();
			$attr_it->moveNext();
		}

		return $this->references;
	}

	function getAttributes() {
		return array_keys($this->getReferenceNames());
	}

	function map( &$parms )
	{
		$attributes = $this->getAttributesInfo();
		foreach( $attributes as $id => $attr ) {
			if ( $this->getTypeIt($attr)->get('ReferenceName') == 'computed' ) {
				if ( $attr['name'] == 'UID' ) continue;
				if ( $parms['RecordCreated'] != '' || !in_array($parms[$attr['name']], array('')) ) {
					$parms[$attr['name']] = $this->computeFormula($parms, $attr['default']);
				}
			}
		}
	}

 	function add( $object_id, $parms )
 	{
		$this->set($object_id, $parms);

		foreach( $this->getAttributesInfo() as $attr_id => $attr ) {
			if ( $attr['name'] == 'UID' && in_array($parms[$attr['name']], array('',$attr['default'])) )
			{
				$idAttribute = $this->getObject()->getIdAttribute();
				$parms[$idAttribute] = $object_id;
				$uid = DAL::Instance()->Escape(addslashes($this->computeFormula($parms, $attr['default'])));
				$sql = "UPDATE ".$this->getObject()->getEntityRefName()." w SET w.UID = '".$uid."' WHERE w.".$idAttribute." IN (".join(",", array($object_id)).")";
				DAL::Instance()->Query( $sql );
			}
		}
 	}

 	function modify( $object_id, $parms )
 	{
		$this->set($object_id, $parms);
 	}
 	
 	protected function set( $object_id, $parms )
 	{
 	 	$attributes = $this->getAttributesInfo();
 		$ids = array_keys($attributes);

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
 			if ( $value_it->getId() == '' ) {
 				// append
 				$value_parms = array(
 					'CustomAttribute' => $attr['id'],
 					'ObjectId' => $object_id
 				);
 				
 				$this->setValueParms( $attr, $parms, $value_parms );
 				$value_it->object->add_parms( $value_parms );
 			}
 			else {
 				// update
 				$value_column = $this->getTypeIt($attr)->getValueColumn();
	 			
	 			$value_parms = array(
	 				$value_column => $value_it->getHtmlDecoded( $value_column )
	 			);

	 			$this->setValueParms( $attr, $parms, $value_parms );
	 			$value->modify_parms($value_it->getId(),
					array_merge(
						$value_parms,
						array (
							'VPD' => $value->getVpdValue()
						)
					));
 			}
 		}
 	}

 	function afterDelete( $object_it )
 	{
 		$attributes = $this->getAttributesInfo();
 		$ids = array_keys($attributes);

 		$value = getFactory()->getObject('pm_AttributeValue');
 		$value_it = $value->getByRefArray(
 			array( 'CustomAttribute' => $ids,
 				   'ObjectId' => $object_it->getId() )
 		);
 		
 		while( !$value_it->end() ) 
 		{
			$value->delete($value_it->getId());
 			$value_it->moveNext();
 		}
 	}
 	
 	function setValueParms( $attribute, $parms, & $value_parms )
 	{
		$value_column = $this->getTypeIt($attribute)->getValueColumn();
 		
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
		$algorithm = defined('MYSQL_ENCRYPTION_ALGORITHM') ? MYSQL_ENCRYPTION_ALGORITHM : 'DES';

 		$attributes = $this->getAttributesInfo();
		$attribute_ids = $this->getReferenceNames();

 		$object = $this->getObject();
		$attribute_data = array();
		
		foreach( $attributes as $attr_id => $attr ) {
 		    $attribute_data[$attr['name']] = $attr;
 		}

 		foreach( $attribute_data as $ref_name => $attr )
 		{
			if ( !preg_match("/^[a-zA-Z][a-zA-Z0-9\_]+$/i", $attr['name']) ) continue;

			$type_it = $this->getTypeIt($attr);
			$value_column = $type_it->getValueColumn();

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
 				"  WHERE cav.ObjectId = ".$this->getPK($alias).
				"    AND cav.VPD = ".$alias.".VPD ".
 				"    AND cav.CustomAttribute IN (".join(',',$attribute_ids[$ref_name]).") LIMIT 1) `".trim($attr['name'])."` "
 			);
 		}

 		return $columns;
 	}

	public function __sleep() {
		return array_merge(
			parent::__sleep(), array('attrs', 'references')
		);
	}

	protected function computeFormula( $data, $formula )
	{
		$object_it = $this->getObject()->createCachedIterator(array($data));
		return preg_replace_callback('/\{([^\}]+)\}/',
			function($match) use ($object_it)
			{
				$object = $object_it->object;
				list($path,$default) = preg_split('/,/', $match[1]);
				$attributes = preg_split('/\./', $path);
				foreach( $attributes as $caption ) {
					if ( strcasecmp($caption,'ИД') == 0 ) {
						$refName = $object->getIdAttribute();
					}
					else {
						$refName = $object->getAttributeByCaption($caption);
					}
					if ( $object->IsReference($refName) ) {
						$object_it = $object_it->getRef($refName);
						$object = $object_it->object;
					}
					else {
						if ( $refName == $object->getIdAttribute() ) {
							$id = $object_it->get($refName);
							if ( $id == '' ) return "{".$caption."}";
							return str_pad($id, 6, '0', STR_PAD_LEFT);
						}
						else {
							return $object_it->get($refName) != '' ? $object_it->get($refName) : $default;
						}
					}
				}
				return $match[0];
			},
			$formula
		);
	}

	protected function getTypeIt( $attribute )
	{
		$data = is_array($attribute['type']) ? $attribute['type'] : array();
		return getFactory()->getObject('CustomAttributeType')->createCachedIterator(array($data));
	}
}