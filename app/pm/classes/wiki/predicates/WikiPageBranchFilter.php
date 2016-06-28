<?php

class WikiPageBranchFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = array_filter( preg_split('/,/', $filter), function( $value ) {
 			return !in_array($value, array('','none'));
 		});
 		
 		if ( count($ids) < 1 ) {
			$sql = " 1 = 2 ";
		}
		else {
			$sql = " EXISTS (SELECT 1 FROM cms_Snapshot b " .
				   "  		  WHERE b.ObjectId = t.DocumentId " .
				   "		    AND b.Caption IN ('" . join("','", $ids) . "') " .
				   "			AND b.ObjectClass = '" . get_class($this->getObject()) . "' " .
				   "			AND b.Type = 'branch') ";
		}

		if ( in_array('none', preg_split('/,/', $filter)) )
		{
			$sql .= " OR NOT EXISTS (SELECT 1 FROM cms_Snapshot b ".
					"  				  WHERE b.ObjectId = t.DocumentId ".
					"				    AND b.ObjectClass = '".get_class($this->getObject())."' ".
					"					AND b.Type = 'branch') ";
		}

		return " AND (".$sql.") ";
 	}
}
