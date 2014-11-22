<?php

class FilterSearchAttributesPredicate extends FilterPredicate
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
 		
 		return " AND MATCH (".join($this->attributes, ',').") AGAINST ('".join(' ',$words)."' IN BOOLEAN MODE) ";
 	}
}
