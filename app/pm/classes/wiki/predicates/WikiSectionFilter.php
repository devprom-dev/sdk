<?php

class WikiSectionFilter extends FilterPredicate
{
 	function WikiSectionFilter()
 	{
 		parent::FilterPredicate('section');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND IsTemplate = 0 ";
 	}
}
