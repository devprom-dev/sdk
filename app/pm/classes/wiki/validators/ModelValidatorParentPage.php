<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorParentPage extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		if ( is_numeric($parms['ParentPage']) ) return "";

		$caption = trim(preg_replace(REGEX_UID, '', $parms['ParentPage']));
		if ( $caption != '' ) {
            $parms['ParentPage'] = $object->getByRef('Caption', $caption)->getId();
            if ( $parms['ParentPage'] == '' ) {
                $parms['ParentPage'] = $object->add_parms(
                    array (
                        'Caption' => $caption,
                        'IsTemplate' => 0,
                        'OrderNum' => 1
                    )
                );
            }
		}

		return "";
	}
}