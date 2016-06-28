<?php

class CustomAttributeSearchPredicate extends FilterPredicate
{
	private $attributes = array();
	
	function __construct( $phrase, $attributes )
	{
		$this->attributes = $attributes;
		parent::__construct($phrase);
	}
	
 	function _predicate( $filter )
 	{
		$clauses = array();
		foreach( SearchRules::getSearchItems($filter) as $word ) {
			$clauses[] = " av.StringValue LIKE '%".$word."%' OR av.TextValue LIKE '%".$word."%' ";
		}
 		return " AND EXISTS (SELECT 1 FROM pm_CustomAttribute at, pm_AttributeValue av
 		 				      WHERE at.EntityReferenceName = '".strtolower(get_class($this->getObject()))."'
 		 				        AND at.pm_CustomAttributeId = av.CustomAttribute
 		 				        AND av.ObjectId = t.".$this->getObject()->getIdAttribute()."
 		 				        AND (".join(" OR ", $clauses).") ) ";
 	}
}
