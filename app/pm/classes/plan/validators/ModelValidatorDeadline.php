<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorEmbeddedForm.php";

class ModelValidatorDeadline extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		$deadline = new ModelValidatorEmbeddedForm('Deadlines', 'Deadline');
		
		$result_1 = $deadline->validate($object, $parms);

		$milestone = new ModelValidatorEmbeddedForm('Deadlines', 'ObjectId');

		$result_2 = $milestone->validate($object, $parms);
		
		return $result_1 != "" && $result_2 != "" ? $result_1 : ""; 
	}
}