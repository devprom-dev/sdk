<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelProjectTemplateValidator extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
        $filePath = $object->getTemplatePath(trim(basename($parms['FileName'])));
        if ( !file_exists($filePath) ) {
            return sprintf(text(2938), $filePath);
        }
		return "";
	}
}