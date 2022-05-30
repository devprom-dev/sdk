<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class ModelValidatorTaskDeadlines extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( $parms['PlannedStartDate'] == '' || $parms['PlannedFinishDate'] == '' ) return "";
		
		$mapper = new ModelDataTypeMappingDate();
		return $mapper->map($parms['PlannedStartDate']) <= $mapper->map($parms['PlannedFinishDate']) ? "" : text(713);
	}
}