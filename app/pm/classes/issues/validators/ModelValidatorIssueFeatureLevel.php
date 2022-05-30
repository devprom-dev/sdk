<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorIssueFeatureLevel extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( $parms['Function'] == '' ) return "";

		$feature_it = getFactory()->getObject('pm_Function')->getExact($parms['Function']);
		if ( $feature_it->getId() == '' ) return "";

		if ( $feature_it->get('Type') == '' || $feature_it->object->getAttributeType('Type') == '' ) return "";

		$type_it = getFactory()->getObject('FeatureType')->getRegistry()->Query(
			array(
				new FilterInPredicate($feature_it->get('Type'))
			)
		);
		if ( $type_it->get('HasIssues') != 'N' ) return "";

		return text(1917);
	}
}