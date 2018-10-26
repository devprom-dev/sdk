<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorChildrenLevels extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms['ParentFeature'] == '' ) return "";
		if ( $parms['Type'] == '' ) return "";

        $hasFeatureLevelRules = join('',getFactory()->getObject('FeatureType')->getRegistry()->Query(
                array(
                    new FilterBaseVpdPredicate()
                )
            )->fieldToArray('ChildrenLevels')) != '';

		$feature_it = $object->getExact($parms['ParentFeature']);
		$parent_system_name = $feature_it->getRef('Type')->get('ChildrenLevels');
		if ( $parent_system_name == '' && !$hasFeatureLevelRules ) return "";
		
		$type_it = getFactory()->getObject('FeatureType')->getExact($parms['Type']);
		$self_system_name = strtolower(trim($type_it->get('ReferenceName')));

		$allowed_levels = array();
		foreach( preg_split('/,/', $parent_system_name) as $level ) {
			$allowed_levels[] = strtolower(trim($level));
		}
		
		return in_array($self_system_name, $allowed_levels) ? "" : text(1920);
	}
}