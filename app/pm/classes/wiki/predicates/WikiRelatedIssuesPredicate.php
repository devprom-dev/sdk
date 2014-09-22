<?php

class WikiRelatedIssuesPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$request = $model_factory->getObject('pm_ChangeRequest');
 		$request_it = $request->getExact( preg_split('/-/', trim($filter, '-')) );
 		
 		if ( $request_it->count() < 1 ) return " AND 1 = 2";
 		$object = $this->getObject();
 		
		return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestTrace r " .
			   "			  WHERE r.ObjectClass = '".strtolower(get_class($object))."'" .
			   "				AND r.ObjectId = t.WikiPageId ".
			   " 			    AND r.ChangeRequest IN (".join($request_it->idsToArray(), ',').") ) ";
 	}
} 
