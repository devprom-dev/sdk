<?php

class WikiContentFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'empty':
 				return " AND t.Content IS NULL ";

 			case 'nonempty':
 				return " AND t.Content IS NOT NULL ";
 				
 			default:
 				return " AND 1 = 2 ";
 		}
 	}
}
