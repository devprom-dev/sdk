<?php

class WikiPageSourcePagePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
		 		return " AND NOT EXISTS (SELECT 1 FROM WikiPageTrace tr ".
		 			   " 			  	  WHERE tr.TargetPage = t.WikiPageId )";
 			default:
                $likes = array();
 				$ids = \TextUtils::parseIds($filter);
                foreach( $ids as $id ) {
                    $likes[] = " p.ParentPath LIKE '%,".$id.",%' ";
                }
                if ( count($likes) < 1 ) return " AND 1 = 2 ";

		 		return " AND EXISTS (SELECT 1 FROM WikiPageTrace tr, WikiPage p ".
		 			   " 			  WHERE p.WikiPageId = tr.SourcePage AND (".join(' OR ', $likes).") ".
		 			   "				AND tr.TargetPage = t.WikiPageId )";
 		}
 	}
}
