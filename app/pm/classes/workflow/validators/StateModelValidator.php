<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class StateModelValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
	    if ( $parms['ReferenceName'] == '' ) return "";
        if ( $parms['ObjectClass'] == '' ) return "";

        $object_it = $object->getRegistry()->Query(
            array(
                new FilterBaseVPDPredicate(),
                new FilterAttributePredicate('ReferenceName', $parms['ReferenceName']),
                new FilterAttributePredicate('ObjectClass', $parms['ObjectClass']),
                new FilterNotInPredicate($parms[$object->getIdAttribute()])
            )
        );
        if ( $object_it->count() > 0 ) return text(1121);
        if ( !\TextUtils::checkReferenceName($parms['ReferenceName']) ) return text(1126);

		return "";
	}
}