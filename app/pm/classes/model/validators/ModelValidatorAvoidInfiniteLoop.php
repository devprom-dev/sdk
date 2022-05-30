<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorAvoidInfiniteLoop extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( $parms[$object->getIdAttribute()] == '' ) return "";

        $parentAttribute = array_shift($object->getAttributesByGroup('hierarchy-parent'));
		if ( $parms[$parentAttribute] == '' ) return "";
		
		$ids = $object->getRegistry()->QueryKeys(
                array (
                    new \ParentTransitiveFilter($parms[$object->getIdAttribute()])
                )
			)->idsToArray();
        return in_array($parms[$parentAttribute], $ids) ? text(1903) : "";
	}
}