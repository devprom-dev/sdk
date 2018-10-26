<?php

include_once "ModelValidatorInstance.php";

class ModelValidatorEmbeddedForm extends ModelValidatorInstance
{
	private $validate_field = '';

	public function __construct( $validate_field ) {
		$this->validate_field = $validate_field;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$rows = array();

		if ( !$object->IsAttributeRequired($this->validate_field) ) return "";
		if ( $parms[$this->validate_field] != '' ) return "";

		$embeddedFormId = '';
        foreach( array_keys($parms) as $field ) {
            if (preg_match('/embeddedFieldName([\d]+)/', $field, $matches) && $parms[$field] == $this->validate_field) {
                $embeddedFormId = $matches[1];
            }
        }
        if ( $embeddedFormId == '' ) return "";

		foreach( array_keys($parms) as $field )
		{
			 if ( preg_match('/F'.$embeddedFormId.'_Id([\d]+)/i', $field, $matches) && $parms[$field] != '' ) {
			 	if ( $parms['F'.$embeddedFormId.'_Delete'.$matches[1]] < 1 ) $rows[] = $parms[$field];
			 }
		}

		return count($rows) > 0 ? "" : text(2).': '.$object->getAttributeUserName($this->validate_field);
	}
}