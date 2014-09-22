<?php

class WikiContentFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'empty':
 				return " AND t.ContentPresents = 'N' ";

 			case 'nonempty':
 				return " AND t.ContentPresents = 'Y' ";
 				
 			default:
 				return " AND 1 = 2 ";
 		}
 	}
}
