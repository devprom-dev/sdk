<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingReference extends ModelDataTypeMapping
{
    private $entityReferenceName = '';

	public function applicable( $type_name )
	{
        if ( $type_name == 'reference' ) return true;
        $matches = array();
	    if ( !preg_match('/([a-zA-Z_]+)Id/i', $type_name, $matches) ) return false;
        $matches[1] = str_replace('ref_', '', strtolower($matches[1]));
        if ( !class_exists($matches[1]) ) return false;
        $this->entityReferenceName = $matches[1];
        return true;
	}
	
	public function map( $value )
	{
        if ( is_null($value) || $value == 'NULL' ) $value = '';
	    if ( is_array($value) ) {
	        $value = join(',', array_filter($value, function($item) {
	            return $item != 'NULL' && strlen($item) > 0;
	        }));
        }

        if ( $this->entityReferenceName != '' && $value != '' )
        {
            $className = getFactory()->getClass($this->entityReferenceName);
            if ( class_exists($className) ) {
                $reference = getFactory()->getObject($className);
                if ( !$reference->IsPersistable() ) return $value;
                if (in_array($reference->getEntityRefName(), array('cms_User', 'entity'))) return $value;

                $alternativeKey = array_shift($reference->getAttributesByGroup('alternative-key'));
                if ( $alternativeKey == '' && $reference->hasAttribute('Caption') ) $alternativeKey = 'Caption';

                if ($alternativeKey != '') {
                    $resultValues = array();
                    $accessPolicy = getFactory()->getAccessPolicy();

                    foreach( \TextUtils::parseItems($value) as $valueItem ) {
                        if (is_numeric($valueItem)) {
                            $objectIt = $reference->getExact($valueItem);
                            if ( $objectIt->getId() != '' ) {
                                $resultValues[] = $objectIt->getId();
                            }
                        }
                        else {
                            if ( $accessPolicy->can_modify($reference) && $accessPolicy->can_create($reference) ) {
                                $objectIt = $reference->getRegistry()->Merge(
                                    array(
                                        $alternativeKey => $valueItem
                                    )
                                );
                                if (is_object($objectIt)) {
                                    $resultValues[] = $objectIt->getId();
                                }
                            }
                        }
                    }
                    return join(',',$resultValues);
                }
            }
        }
		return $value;
	}
}