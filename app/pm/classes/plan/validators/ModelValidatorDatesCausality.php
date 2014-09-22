<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMappingDate.php";

class ModelValidatorDatesCausality extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( $parms['StartDate'] == '' || $parms['FinishDate'] == '' ) return "";
		
		$mapper = new ModelDataTypeMappingDate();

		return $mapper->map($parms['StartDate']) <= $mapper->map($parms['FinishDate']) ? "" : text(713); 		
	}
}