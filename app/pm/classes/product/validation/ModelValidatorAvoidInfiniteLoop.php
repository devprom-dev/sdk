<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";
include_once SERVER_ROOT_PATH."pm/classes/common/predicates/ParentTransitiveFilter.php";

class ModelValidatorAvoidInfiniteLoop extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms[$object->getIdAttribute()] == '' ) return "";
		if ( $parms['ParentFeature'] == '' ) return "";
		
		$ids = $object->getRegistry()->Query(
					array (
							new ParentTransitiveFilter($parms[$object->getIdAttribute()])
					)
			)->idsToArray();

		return in_array($parms['ParentFeature'], $ids) ? text(1903) : ""; 		
	}
}