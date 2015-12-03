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
 		$words = preg_split('/\s+/', $filter);
		
		foreach ( $words as $key => $word )
		{
			if ( $word[0] != '+' && $word[0] != '-' ) $words[$key] = '+'.$word;
			$words[$key] .= '*';
		}
 		
 		return " AND EXISTS (SELECT 1 FROM pm_CustomAttribute at, pm_AttributeValue av
 		 				      WHERE at.EntityReferenceName = '".strtolower(get_class($this->getObject()))."'
 		 				        AND at.pm_CustomAttributeId = av.CustomAttribute
 		 				        AND av.ObjectId = t.".$this->getObject()->getIdAttribute()."
 		 				        AND MATCH (av.StringValue, av.TextValue) AGAINST ('".join(' ',$words)."' IN BOOLEAN MODE)) ";
 	}
}
