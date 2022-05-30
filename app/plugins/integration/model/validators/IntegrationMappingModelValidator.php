<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class IntegrationMappingModelValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( $parms['MappingSettings'] == '' ) return "";

		$result = json_decode($parms['MappingSettings'], true);
		if ( is_array($result) ) return "";

		$text = text('integration13');
		if ( function_exists('json_last_error_msg') ) {
			$text = str_replace('%1', json_last_error_msg(), $text);
		}
		else {
			$text = str_replace('%1', json_last_error(), $text);
		}
		return $text;
	}
}