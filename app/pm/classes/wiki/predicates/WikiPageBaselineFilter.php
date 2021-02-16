<?php

class WikiPageBaselineFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $sqls = array();
 		$ids = \TextUtils::parseItems($filter);
 		if ( count($ids) < 1 ) {
			$sqls[] = " 1 = 2 ";
		}
		else {
            $snapshotIt = getFactory()->getObject('cms_Snapshot')->getExact($ids);
            if ( $snapshotIt->count() < 1 ) {
                $snapshotIt = getFactory()->getObject('cms_Snapshot')
                    ->getByRef('Caption', $ids);
            }
            $sqls[] = " EXISTS (SELECT 1 FROM cms_Snapshot b 
                  		  WHERE b.ObjectId = t.DocumentId 
                		    AND b.cms_SnapshotId IN (".join(',',$snapshotIt->idsToArray()).") 
                			AND b.ObjectClass = '" . get_class($this->getObject()) . "' ) ";
		}

		if ( in_array('none', $ids) ) {
            $sqls[] = " NOT EXISTS (SELECT 1 FROM cms_Snapshot b ".
					"  				  WHERE b.ObjectId = t.DocumentId ".
					"				    AND b.ObjectClass = '".get_class($this->getObject())."' ) ";
		}

		return " AND (".join(" OR ", $sqls).") ";
 	}
}
