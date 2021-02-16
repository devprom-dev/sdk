<?php

class WikiSectionFilter extends FilterPredicate
{
 	function WikiSectionFilter() {
 		parent::__construct('section');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND IsTemplate = 0 ";
 	}
}
