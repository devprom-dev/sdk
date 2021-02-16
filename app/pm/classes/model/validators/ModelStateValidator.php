<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelStateValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
	    if ( !$object instanceof MetaobjectStatable ) return "";
	    if ( $parms['State'] == '' ) return "";

	    $statesAvailable = \WorkflowScheme::Instance()->getStates($object);
	    $states = \TextUtils::parseItems($parms['State']);

	    $commonStates = array_intersect($statesAvailable, $states);
	    if ( count($commonStates) < 1 ) return text(2695);

	    $parms['State'] = array_shift($commonStates);
	    return "";
	}
}