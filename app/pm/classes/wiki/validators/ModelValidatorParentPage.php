<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorParentPage extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( is_numeric($parms['ParentPage']) ) return "";
		
		$parms['ParentPage'] = $object->add_parms(
				array (
						'Caption' => $parms['ParentPage'],
						'IsTemplate' => 0,
						'OrderNum' => 1
				)
		);
		
		return "";
	}
}