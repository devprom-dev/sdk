<?php
include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingReference extends ModelDataTypeMapping
{
    private $entityReferenceName = '';

	public function applicable( $type_name )
	{
        if ( $type_name == 'reference' ) return true;
        $matches = array();
	    if ( !preg_match('/([a-zA-Z_]+)id/i', strtolower($type_name), $matches) ) return false;
        $matches[1] = str_replace('ref_', '', strtolower($matches[1]));
        $className = getFactory()->getClass($matches[1]);
        if ( !class_exists($className) ) return false;
        $this->entityReferenceName = $className;
        return true;
	}
	
	public function map( $value, array $groups = array() )
	{
        if ( \TextUtils::isNullValue($value) ) return '';

	    if ( is_array($value) ) {
	        $value = join(',', array_filter($value, function($item) {
	            return strtolower($item) != 'null' && strlen($item) > 0;
	        }));
        }
	    $uidService = new ObjectUID;

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

                    foreach( \TextUtils::parseItems($value) as $valueItem )
                    {
                        $matches = array();
                        if ( preg_match('/\[([^\]]+)\]/i', $valueItem, $matches) ) {
                            $uid = $matches[1];
                            $objectIt = $uidService->getObjectIt($uid);
                            if ( $objectIt->getId() != '' ) {
                                $resultValues[] = $objectIt->getId();
                                continue;
                            }
                        }

                        if (ctype_digit($valueItem)) {
                            $objectIt = $reference->getExact($valueItem);
                            if ( $objectIt->getId() != '' ) {
                                $resultValues[] = $objectIt->getId();
                            }
                        }
                        else if ( $accessPolicy->can_modify($reference) && $accessPolicy->can_create($reference) ) {
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
                    return join(',',$resultValues);
                }
            }
        }
		return $value;
	}
}