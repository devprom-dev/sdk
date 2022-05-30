<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorIssueTasks extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( !$object->IsAttributeRequired('Tasks') ) return "";

		$keys = array_filter( array_keys($parms), function($key) use ($parms) {
            return preg_match('/embeddedActive\d+/', $key) && $parms[$key] == 'Y';
		});
		
		return count($keys) > 0 ? "" : text(1848); 		
	}
}