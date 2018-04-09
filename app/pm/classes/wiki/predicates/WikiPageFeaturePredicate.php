<?php

class WikiPageFeaturePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$feature_it = getFactory()->getObject('Feature')->getExact(
		    \TextUtils::parseIds($filter)
        );
		if ( $feature_it->getId() < 1 ) return " AND 1 = 2 ";

		$ids = $feature_it->idsToArray();
 		return " AND (
 		            EXISTS( 
                        SELECT 1 FROM pm_FunctionTrace l 
                         WHERE l.Feature IN (".join(',', $ids).") 
                           AND l.ObjectId = t.WikiPageId 
                           AND l.ObjectClass = '".get_class($this->getObject())."') 
 		            OR EXISTS(
 		                SELECT 1 FROM pm_FunctionTrace r, WikiPageTrace tr
 		                 WHERE r.Feature IN (".join(',', $ids).")
 		                   AND r.ObjectId = tr.SourcePage 
 		                   AND tr.Type = 'coverage'
                		   AND tr.TargetPage = t.WikiPageId)
                    OR EXISTS(
                        SELECT 1 FROM pm_ChangeRequest r, pm_ChangeRequestTrace tr
                         WHERE r.Function IN (".join(',', $ids).")
 		                   AND r.pm_ChangeRequestId = tr.ChangeRequest 
                           AND tr.ObjectId = t.WikiPageId
 		            	   AND tr.ObjectClass = '".strtolower(get_class($this->getObject()))."')
 		        )";
 	}
} 
