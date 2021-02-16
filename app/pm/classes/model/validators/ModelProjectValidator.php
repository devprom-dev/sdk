<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelProjectValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
	    if ( !array_key_exists('Project', $parms) ) return "";
		if ( !$object->IsAttributeRequired('Project') ) return "";
		if ( $parms['Project'] < 1 ) return text(2).': '.translate($object->getAttributeUserName('Project'));

		$project_it = getFactory()->getObject('Project')->getExact($parms['Project']);
		if ( $project_it->getId() < 1 ) return text(2).': '.translate($object->getAttributeUserName('Project'));

		$parms['VPD'] = $project_it->get('VPD');
		return "";
	}
}