<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPasswordPersister.php";

class CustomAttributesPersister extends ObjectSQLPersister
{
 	private $attrs = array();
	private $references = array();

	function setObject( $object )
	{
		parent::setObject($object);
		$this->getAttributesInfo();
	}

    function IsPersisterImportant() {
        return true;
    }

    protected function getAttributeIt() {
        return getFactory()->getObject('pm_CustomAttribute')->getByEntity($this->getObject());
    }

 	protected function getAttributesInfo()
 	{
 		if ( count($this->attrs) > 0 ) return $this->attrs;

        $attr_it = $this->getAttributeIt();
 		while ( !$attr_it->end() )
		{
 			$this->attrs[$attr_it->getId()] = array (
			    'id' => $attr_it->getId(),
 				'name' => $attr_it->get('ReferenceName'),
 				'type' => $attr_it->getRef('AttributeType')->getData(),
				'default' => $attr_it->getHtmlDecoded('DefaultValue'),
                'VPD' => $attr_it->get('VPD'),
 			);
            $this->references[strtolower($attr_it->get('ReferenceName'))][] = $attr_it->getId();
 			$attr_it->moveNext();
 		}

 		return $this->attrs;
 	}

 	function add( $object_id, $parms ) {
		$this->set($object_id, $parms, true);
 	}

    function modify( $object_id, $parms ) {
        $this->set($object_id, $parms);
    }

 	protected function set( $object_id, $parms, $useDefaults = false, $newOnly = true )
 	{
 	 	$attributes = $this->getAttributesInfo();
        if ( count($attributes) < 1 ) return;

 		$value = getFactory()->getObject('pm_AttributeValue');
        $valueRegistry = $value->getRegistry();
        $objectAttributes = array();

 		foreach( $attributes as $attr )
 		{
            if ( $this->getTypeIt($attr)->get('ReferenceName') == 'computed' ) {
                if ( $attr['name'] == 'UID' ) {
                    continue;
                }
                else {
                    if ( count($objectAttributes) < 1 ) {
                        $objectAttributes = $this->getObject()->getRegistryBase()->Query(
                                array(
                                    new FilterInPredicate($object_id)
                                )
                            )->getData();
                    }
                    $parms[$attr['name']] = $this->computeFormula($objectAttributes, $attr['default']);
                }
            }

            if ( !array_key_exists($attr['name'], $parms) && !array_key_exists($attr['name'].'OnForm', $parms) ) {
                if ( $attr['default'] == '' || !$useDefaults ) continue;
                $parms[$attr['name']] = $attr['default'];
            }

            $value_parms = array(
                'CustomAttribute' => $attr['id'],
                'ObjectId' => $object_id
            );
            $this->setValueParms( $attr, $parms, $value_parms );

            if ( $newOnly ) {
                $cnt = $valueRegistry->Count(array(
                    new FilterAttributePredicate('CustomAttribute', $value_parms['CustomAttribute']),
                    new FilterAttributePredicate('ObjectId', $value_parms['ObjectId'])
                ));
                if ( $cnt < 1 ) {
                    $valueRegistry->Create($value_parms);
                }
            }
            else {
                $valueRegistry->Merge(
                    $value_parms,
                    array(
                        'CustomAttribute',
                        'ObjectId'
                    )
                );
            }
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
 	    $attributeTypeIt = $this->getTypeIt($attribute);
		$value_column = $attributeTypeIt->getValueColumn();

 		if ( !array_key_exists( $attribute['name'], $parms ) )
 		{
 		    if ( $parms[$attribute['name'].'OnForm'] == 'Y' ) {
                $parms[$attribute['name']] = 'N';
            }
 		    else if ( $value_parms[$value_column] != '' ) {
                $parms[$attribute['name']] = $value_parms[$value_column];
            }
 		}
 		elseif ( $attributeTypeIt->get('ReferenceName') != 'wysiwyg' )
 		{
 		    if ( is_array($parms[$attribute['name']]) ) {
 		        $parms[$attribute['name']] = join(',', $parms[$attribute['name']]);
            }
 			$parms[$attribute['name']] = html_entity_decode(
 			    $parms[$attribute['name']], ENT_QUOTES | ENT_HTML401, APP_ENCODING);
 		}
 		
 		$value_parms[$value_column] = $parms[$attribute['name']];
 	}
 	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
		$algorithm = defined('MYSQL_ENCRYPTION_ALGORITHM') ? MYSQL_ENCRYPTION_ALGORITHM : 'DES';

 		$attributes = $this->getAttributesInfo();
		$attribute_data = array();

		foreach( $attributes as $attr_id => $attr ) {
 		    $attribute_data[strtolower($attr['name'])] = $attr;
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
 				"    AND cav.CustomAttribute IN (".join(',',$this->references[$ref_name]).") LIMIT 1) `".trim($attr['name'])."` "
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