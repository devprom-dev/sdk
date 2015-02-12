<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorIssueFeatureLevel extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms['Function'] == '' ) return "";
		
		$feature_it = getFactory()->getObject('pm_Function')->getExact($parms['Function']);

		if ( $feature_it->getId() == '' ) return "";
		if ( $feature_it->get('Type') == '' ) return "";
		if ( $feature_it->getRef('Type')->get('HasIssues') == 'Y' ) return ""; 
		
		return text(1917); 		
	}
}