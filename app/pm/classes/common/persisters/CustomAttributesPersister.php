<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

use Devprom\ProjectBundle\Service\Model\ModelService;
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
				'default' => $attr_it->getHtmlDecoded('DefaultValue'),
                'VPD' => $attr_it->get('VPD'),
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

 		$value = getFactory()->getObject('pm_AttributeValue');
        $value->disableVpd();
        $valueRegistry = $value->getRegistry();

 		foreach( $attributes as $attr_id => $attr ) {
 		    if ( $parms['VPD'] != '' && $parms['VPD'] != $attr['VPD'] ) continue;

            if ( $this->getTypeIt($attr)->get('ReferenceName') == 'computed' )
            {
                $objectAttributes = $this->getObject()->getRegistryBase()->Query(
                    array(
                        new FilterInPredicate($object_id)
                    )
                )->getData();
                $idAttribute = $this->getObject()->getIdAttribute();
                $value = $this->computeFormula($objectAttributes, $attr['default']);

                if ( $attr['name'] == 'UID' )
                {
                    if ( $this->getObject()->IsAttributeStored('UID') && in_array($parms[$attr['name']], array('',$attr['default'])) && $objectAttributes['TraceSourceRequirementBaselines'] == '' )
                    {
                        $value = DAL::Instance()->Escape($value);
                        $objectAttributes['UID'] = DAL::Instance()->Escape($objectAttributes['UID']);

                        DAL::Instance()->Query(
                            "UPDATE ".$this->getObject()->getEntityRefName()." w SET w.UID = '".$value."' WHERE w.".$idAttribute." IN (".join(",", array($object_id)).")"
                        );

                        if ( $objectAttributes['UID'] != '' ) {
                            DAL::Instance()->Query(
                                "UPDATE ".$this->getObject()->getEntityRefName()." w SET w.UID = '".$value."' WHERE w.UID = '".$objectAttributes['UID']."'"
                            );
                        }
                    }
                    continue;
                }
                else {
                    $parms[$attr['name']] = $value;
                }
            }

            if ( !array_key_exists($attr['name'], $parms) ) continue;

            $value_parms = array(
                'CustomAttribute' => $attr['id'],
                'ObjectId' => $object_id
            );
            $this->setValueParms( $attr, $parms, $value_parms );

            $valueRegistry->Merge(
                $value_parms,
                array(
                    'CustomAttribute',
                    'ObjectId'
                )
            );

 		}
 	}

 	function afterDelete( $object_it )
 	{
 		$attributes = $this->getAttributesInfo();
 		$ids = array_keys($attributes);

 		$value = getFactory()->getObject('pm_AttributeValue');
 		$value_it = $value->getRegistry()->Query(
 		    array(
 		        new FilterAttributePredicate('CustomAttribute', $ids),
                new FilterAttributePredicate('ObjectId', $object_it->getId())
            )
        );
 		while( !$value_it->end() ) {
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
			if ( !\TextUtils::checkDatabaseColumnName($attr['name']) ) continue;
            if ( $attr['name'] == 'UID' ) continue;

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
        return trim(
            DAL::Instance()->Escape(
                addslashes(
                    array_shift(
                        ModelService::computeFormula(
                            $this->getObject()->createCachedIterator(array($data)),
                            $formula
                        )
                    )
                )
            )
        );
	}

	protected function getTypeIt( $attribute )
	{
		$data = is_array($attribute['type']) ? $attribute['type'] : array();
		return getFactory()->getObject('CustomAttributeType')->createCachedIterator(array($data));
	}
}