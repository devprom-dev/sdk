<?php

class CustomAttributeObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !$filter instanceof IteratorBase ) return " AND 1 = 2 ";
 	    if ( !is_numeric($filter->getId()) ) return " AND 1 = 2 ";

 	    if ( $filter->object->getEntityRefName() == 'pm_ChangeRequest' ) {
            $classNames = array(
                'Request', 'Issue', 'Increment'
            );
        }
 	    else {
 	        $classNames = array(
 	            get_class($filter->object)
            );
        }
 		return " AND t.EntityReferenceName IN ('".join("','", $classNames)."') 
 		         AND EXISTS (SELECT 1 FROM pm_AttributeValue av 
 		                      WHERE av.ObjectId = ".$filter->getId()."
 		                        AND av.CustomAttribute = t.pm_CustomAttributeId) ";
	}
}