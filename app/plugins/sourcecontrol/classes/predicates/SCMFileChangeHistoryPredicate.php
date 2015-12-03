<?php

class SCMFileChangeHistoryPredicate extends FilterPredicate
{
	private $attribute = '';

	function __construct( $attribute, $value )
	{
		$this->attribute = $attribute;
		parent::__construct($value);
	}
	
 	function _predicate( $filter )
 	{
		$ids = preg_split('/,/', $filter);
		return " AND IFNULL(".$this->getAlias().'.'.$this->attribute.",'".array_pop(array_values($ids))."') IN ('".join("','",$ids)."') ";
 	}
}
