<?php
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class SystemTemplateYamlValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
		if ( $parms['Format'] == '' ) return '';

		try {
			$yaml = new Parser();
			$yaml->parse(htmlentities($parms['Content']));
		}
		catch (ParseException $e) {
			return str_replace('%1', $e->getMessage(), text(2140));
		}

		return '';
	}
}