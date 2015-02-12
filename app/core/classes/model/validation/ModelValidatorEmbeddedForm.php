<?php

include_once "ModelValidatorInstance.php";

class ModelValidatorEmbeddedForm extends ModelValidatorInstance
{
	private $validate_field = '';
	private $check_embedded_field = '';
	
	public function __construct( $validate_field, $check_embedded_field )
	{
		$this->validate_field = $validate_field;
		$this->check_embedded_field = $check_embedded_field;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$rows = array();

		if ( !$object->IsAttributeRequired($this->validate_field) ) return "";
		
		foreach( array_keys($parms) as $field )
		{
			 if ( preg_match('/F[\d]+_'.$this->check_embedded_field.'[\d]+/i', $field, $matches) && $parms[$field] != '' )
			 {
			 	$rows[] = $parms[$field];
			 }

			 if ( preg_match('/F([\d]+)_Id([\d]+)/i', $field, $matches) && $parms[$field] != '' )
			 {
			 	if ( $parms['F'.$matches[1].'_Delete'.$matches[2]] < 1 ) $rows[] = $parms[$field];
			 }
		}

		return count($rows) > 0 ? "" : text(2).': '.$object->getAttributeUserName($this->validate_field);
	}
}