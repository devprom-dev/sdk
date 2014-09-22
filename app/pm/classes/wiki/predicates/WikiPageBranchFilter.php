<?php

class WikiPageBranchFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = array_filter( preg_split('/,/', $filter), function( $value ) {
 			return $value != "";
 		});
 		
 		if ( count($ids) < 1 ) return " AND 1 = 2 ";
 		
 		if ( in_array('none', $ids) )
 		{
 			return " AND NOT EXISTS (SELECT 1 FROM cms_Snapshot b ".
 	 			   "  				  WHERE b.ObjectId = t.WikiPageId ".
 	 			   "				    AND b.ObjectClass = '".get_class($this->getObject())."' ".
 	 			   "					AND b.Type = 'branch') ";
 		}

		return " AND EXISTS (SELECT 1 FROM cms_Snapshot b ".
 			   "  			  WHERE b.ObjectId = t.DocumentId ".
 			   "				AND b.Caption IN ('".join("','", $ids)."') ".
 			   "			    AND b.ObjectClass = '".get_class($this->getObject())."' ".
 			   "				AND b.Type = 'branch') ";
 		
		return $predicate;
 	}
}
