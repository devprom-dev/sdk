<?php

class CustomAttributeObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !$filter instanceof IteratorBase ) return " AND 1 = 2 ";
 	    if ( !is_numeric($filter->getId()) ) return " AND 1 = 2 ";

 	    if ( $filter->object instanceof Request || $filter->object instanceof Increment ) {
            $classNames = array(
                'Request', 'Increment'
            );
        }
 	    else {
 	        $classNames = array(
 	            get_class($filter->object)
            );
        }
        $idsParm = join(',',$filter->idsToArray());
 		return " AND t.EntityReferenceName IN ('".join("','", $classNames)."') 
 		         AND EXISTS (SELECT 1 FROM pm_AttributeValue av 
 		                      WHERE av.ObjectId IN ({$idsParm})
 		                        AND av.CustomAttribute = t.pm_CustomAttributeId) ";
	}
}