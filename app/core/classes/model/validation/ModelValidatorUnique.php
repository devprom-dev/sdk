<?php
include_once "ModelValidatorInstance.php";

class ModelValidatorUnique extends ModelValidatorInstance
{
	private $unique_fields = array();
	
	public function __construct( $fields = array() ) {
		$this->unique_fields = $fields;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$predicates = array();
		$titles = array();

		foreach( $this->unique_fields as $field ) {
			if ( $parms[$field] == '' ) return "";
			$predicates[] = new FilterAttributePredicate($field, $parms[$field]);
			$titles[] = translate($object->getAttributeUserName($field));
		}
		if ( count($titles) < 1 ) return "";

		if ( $object->getVpdValue() != '' && ! $object instanceof Project ) {
            $predicates[] = new FilterBaseVpdPredicate();
        }

		$dup_it = $object->getRegistry()->Query($predicates);

		if ( $dup_it->count() > 0 && $parms[$object->getIdAttribute()] != $dup_it->getId() ) {
			return str_replace('%1', join(',', $titles), text(1176));
		}
		return "";
	}
}