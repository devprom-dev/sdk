<?php

include_once "ModelValidatorInstance.php";

class ModelValidatorUnique extends ModelValidatorInstance
{
	private $unique_fields = array();
	
	public function __construct( $fields = array() )
	{
		$this->unique_fields = $fields;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$predicates = array();
		$titles = array();
		
		foreach( $this->unique_fields as $field )
		{
			if ( $parms[$field] == '' ) continue;
				
			$predicates[] = new FilterAttributePredicate($field, $parms[$field]);
			$titles[] = translate($object->getAttributeUserName($field));
		}
		
		if ( count($predicates) < 1 ) return ""; 
		
		$dup_it = $object->getRegistry()->Query(
				array_merge ( 
						$predicates,
						array (
								new FilterBaseVpdPredicate()
						) 
				)
		);

		if ( $dup_it->count() > 0 && $parms[$object->getIdAttribute()] != $dup_it->getId() )
		{
			return str_replace('%1', join(',', $titles), text(1176));
		}

		return "";
	}
}