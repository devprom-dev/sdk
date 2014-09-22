<?php

class WikiInArchivePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch( $filter )
 		{
 			case 'archived':
 				return " AND t.IsArchived = 'Y' ";
 			
 			default:
 				return " AND IFNULL(t.IsArchived, 'N') = 'N' ";
 		}
 	}
}
