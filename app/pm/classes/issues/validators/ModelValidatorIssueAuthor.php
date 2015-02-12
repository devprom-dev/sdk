<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorIssueAuthor extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( !is_numeric($parms['Author']) )
		{
			$parms['Customer'] = $parms['Author'];
			$parms['Author'] = '';
		}
		return ""; 		
	}
}