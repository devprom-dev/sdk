<?php

class PMWikiSourceFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
		 		return " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace tr ". 
		 			   " 			  	  WHERE tr.TargetPage = t.WikiPageId )";

 			default:
 				$ids = array_filter( preg_split('/,/', $filter), function( $value ) {
 					return $value > 0;
 				});
 				
		 		$object_it = getFactory()->getObject('WikiPage')->getRegistry()->Query(
		 				array( 
		 						count($ids) > 1 ? new FilterInPredicate($ids) : new WikiRootTransitiveFilter($ids[0])
		 				)
				);
		 		
		 		if ( $object_it->count() < 1 ) return " AND 1 = 2 ";
		 		
		 		return " AND EXISTS (SELECT 1 FROM WikiPageTrace tr ". 
		 			   " 			  WHERE tr.SourcePage IN (".join(',',$object_it->idsToArray()).")".
		 			   "				AND tr.TargetPage = t.WikiPageId )";
 		}
 	}
}
